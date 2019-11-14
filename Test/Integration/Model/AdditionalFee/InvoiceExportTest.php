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
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\Sales\Model\Service\InvoiceService;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;

/**
 * Class InvoiceExportTest
 *
 * Assert that additional fee totals are contained in the totals
 * when reading the order, e.g. via REST API calls.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 *
 * @magentoAppIsolation enabled
 */
class InvoiceExportTest extends TestCase
{
    /**
     * @var Invoice
     */
    private static $invoice;

    /**
     * @var Invoice
     */
    private static $invoiceWithFee;

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
    public static function createInvoice()
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Invoice $resource */
        $resource = Bootstrap::getObjectManager()->create(\Magento\Sales\Model\ResourceModel\Order\Invoice::class);

        /** @var InvoiceService $invoiceService */
        $invoiceService = Bootstrap::getObjectManager()->create(InvoiceService::class);

        /** @var \Magento\Sales\Model\Order $order */
        $order = OrderFixture::createOrder(new AddressDe(), [new SimpleProduct()], 'flatrate_flatrate');
        self::$invoice = $invoiceService->prepareInvoice($order);
        $resource->save(self::$invoice);

        /** @var \Magento\Sales\Model\Order $orderWithFee */
        $orderWithFee = OrderFixture::createOrder(new AddressDe(), [new SimpleProduct2()], 'flatrate_flatrate');
        $orderWithFee->addData(self::$additionalFees);
        self::$invoiceWithFee = $invoiceService->prepareInvoice($orderWithFee);
        $resource->save(self::$invoiceWithFee);
    }

    /**
     * @throws \Exception
     */
    public static function createInvoiceRollback()
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
     * @param InvoiceInterface $invoice
     */
    private static function assertTotalsNotLoaded(InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes();

        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFeeInclTax());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFeeInclTax());
    }

    /**
     * @param InvoiceInterface $invoice
     */
    private static function assertTotalsLoaded(InvoiceInterface $invoice)
    {
        $extensionAttributes = $invoice->getExtensionAttributes();

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
     * Assert `InvoiceRepository::get` behaviour.
     *
     * @test
     * @magentoDataFixture createInvoice
     */
    public function getById()
    {
        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = Bootstrap::getObjectManager()->create(InvoiceRepositoryInterface::class);

        $invoice = $invoiceRepository->get(self::$invoice->getEntityId());
        self::assertTotalsNotLoaded($invoice);

        $invoice = $invoiceRepository->get(self::$invoiceWithFee->getEntityId());
        self::assertTotalsLoaded($invoice);
    }

    /**
     * Assert `InvoiceRepository::getList` behaviour.
     *
     * @test
     * @magentoDataFixture createInvoice
     */
    public function getList()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            InvoiceInterface::ENTITY_ID,
            [self::$invoice->getId(),  self::$invoiceWithFee->getId()],
            'in'
        );
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var InvoiceRepositoryInterface $invoiceRepository */
        $invoiceRepository = Bootstrap::getObjectManager()->create(InvoiceRepositoryInterface::class);
        $invoices = $invoiceRepository->getList($searchCriteria)->getItems();

        self::assertNotEmpty($invoices);
        foreach ($invoices as $invoice) {
            $invoice->getEntityId() === self::$invoiceWithFee->getEntityId()
                ? self::assertTotalsLoaded($invoice)
                : self::assertTotalsNotLoaded($invoice);
        }
    }
}
