<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\AdditionalFee;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\InvoiceInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\InvoiceRepositoryInterface;
use Magento\Sales\Model\Order\Invoice;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\InvoiceBuilder;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

/**
 * Invoice export
 *
 * Assert that additional fee totals are contained in the totals
 * when reading the order, e.g. via REST API calls.
 *
 * @magentoAppIsolation enabled
 */
class InvoiceExportTest extends TestCase
{
    /**
     * @var OrderInterface[]
     */
    private static $orders = [];

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
        $order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();
        self::$invoice = InvoiceBuilder::forOrder($order)->build();

        /** @var OrderBuilder $orderBuilder */
        $orderBuilder = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate');
        foreach (self::$additionalFees as $code => $value) {
            $orderBuilder = $orderBuilder->withAdditionalFee($code, $value);
        }
        $orderWithFee = $orderBuilder->build();
        self::$invoiceWithFee = InvoiceBuilder::forOrder($orderWithFee)->build();

        self::$orders = [$order, $orderWithFee];
    }

    /**
     * @throws \Exception
     */
    public static function createInvoiceRollback()
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
