<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\AdditionalFee;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\CreditmemoRepositoryInterface;
use Magento\Sales\Api\Data\CreditmemoInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Creditmemo;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\CreditmemoBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

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
     * @var OrderInterface[]
     */
    private static $orders = [];

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
        $order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
        self::$creditMemo = CreditmemoBuilder::forOrder($order)->build();

        /** @var OrderBuilder $orderBuilder */
        $orderBuilder = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate');
        foreach (self::$additionalFees as $code => $value) {
            $orderBuilder = $orderBuilder->withAdditionalFee($code, $value);
        }
        $orderWithFee = $orderBuilder->build();
        self::$creditMemoWithFee = CreditmemoBuilder::forOrder($orderWithFee)->build();

        self::$orders = [$order, $orderWithFee];
    }

    /**
     * @throws \Exception
     */
    public static function createCreditMemoRollback()
    {
        try {
            $orderFixtures = array_map(
                static function (OrderInterface $order) {
                    return new OrderFixture($order);
                },
                self::$orders
            );

            OrderFixtureRollback::create()->execute(...$orderFixtures);
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
