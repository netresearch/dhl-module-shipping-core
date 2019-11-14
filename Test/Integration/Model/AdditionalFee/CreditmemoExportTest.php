<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\AdditionalFee;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressDe;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\SimpleProduct2;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderFixture;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\Sales\Model\Order\CreditmemoFactory;
use Magento\Sales\Model\Order\CreditmemoRepository;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class CreditmemoExportTest
 *
 * Assert that additional fee totals are contained in the totals
 * when reading the credit memo, e.g. via REST API calls.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 *
 * @magentoAppIsolation enabled
 */
class CreditmemoExportTest extends TestCase
{
    /**
     * @var Creditmemo
     */
    private static $creditMemo;

    /**
     * @var Creditmemo
     */
    private static $creditMemoWithFee;

    /**
     * @var float[]
     */
    private static $additionalFees = [
        TotalsManager::ADDITIONAL_FEE_FIELD_NAME => 12.50,
        TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME => 13.50,
        TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME => 15.50,
        TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME => 17.50,
    ];

    /**
     * @throws \Exception
     */
    public static function createCreditMemo()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Creditmemo $resource */
        $resource = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\ResourceModel\Order\Creditmemo::class);

        /** @var CreditmemoFactory $creditMemoFactory */
        $creditMemoFactory = Bootstrap::getObjectManager()->create(CreditmemoFactory::class);

        /** @var \Magento\Sales\Model\Order $order */
        $order = OrderFixture::createOrder(new AddressDe(), [new SimpleProduct()], 'flatrate_flatrate');
        self::$creditMemo = $creditMemoFactory->createByOrder($order);
        $resource->save(self::$creditMemo);

        /** @var \Magento\Sales\Model\Order $orderWithFee */
        $orderWithFee = OrderFixture::createOrder(new AddressDe(), [new SimpleProduct2()], 'flatrate_flatrate');
        $orderWithFee->addData(self::$additionalFees);
        self::$creditMemoWithFee = $creditMemoFactory->createByOrder($orderWithFee);
        $resource->save(self::$creditMemoWithFee);
    }

    /**
     * @throws \Exception
     */
    public static function createCreditMemoRollback()
    {
        try {
            OrderFixture::rollbackFixtureEntities();
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    /**
     * @param CreditmemoInterface $creditMemo
     */
    private static function assertTotalsNotLoaded(CreditmemoInterface $creditMemo)
    {
        $extensionAttributes = $creditMemo->getExtensionAttributes();

        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFeeInclTax());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFeeInclTax());
    }

    /**
     * @param CreditmemoInterface $creditMemo
     */
    private static function assertTotalsLoaded(CreditmemoInterface $creditMemo)
    {
        $extensionAttributes = $creditMemo->getExtensionAttributes();

        self::assertEquals(
            self::$additionalFees[TotalsManager::ADDITIONAL_FEE_FIELD_NAME],
            $extensionAttributes->getDhlgwAdditionalFee()
        );

        self::assertEquals(
            self::$additionalFees[TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME],
            $extensionAttributes->getDhlgwAdditionalFeeInclTax()
        );

        self::assertEquals(
            self::$additionalFees[TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME],
            $extensionAttributes->getBaseDhlgwAdditionalFee()
        );

        self::assertEquals(
            self::$additionalFees[TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME],
            $extensionAttributes->getBaseDhlgwAdditionalFeeInclTax()
        );
    }

    /**
     * Assert `CreditmemoRepository::get` behaviour.
     *
     * @test
     * @magentoDataFixture createCreditMemo
     */
    public function getById()
    {
        /** @var CreditmemoRepositoryInterface $creditMemoRepository */
        $creditMemoRepository = Bootstrap::getObjectManager()->create(CreditmemoRepositoryInterface::class);

        $creditMemo = $creditMemoRepository->get(self::$creditMemo->getEntityId());
        self::assertTotalsNotLoaded($creditMemo);

        $creditMemo = $creditMemoRepository->get(self::$creditMemoWithFee->getEntityId());
        self::assertTotalsLoaded($creditMemo);
    }

    /**
     * Assert `CreditmemoRepository::getList` behaviour.
     *
     * @test
     * @magentoDataFixture createCreditMemo
     */
    public function getList()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            CreditmemoInterface::ENTITY_ID,
            [self::$creditMemo->getId(),  self::$creditMemoWithFee->getId()],
            'in'
        );
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var CreditmemoRepositoryInterface $creditMemoRepository */
        $creditMemoRepository =  Bootstrap::getObjectManager()->create(CreditmemoRepositoryInterface::class);
        $creditMemos = $creditMemoRepository->getList($searchCriteria)->getItems();

        self::assertNotEmpty($creditMemos);
        foreach ($creditMemos as $creditMemo) {
            $creditMemo->getEntityId() === self::$creditMemoWithFee->getEntityId()
                ? self::assertTotalsLoaded($creditMemo)
                : self::assertTotalsNotLoaded($creditMemo);
        }
    }
}
