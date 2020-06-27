<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model\AdditionalFee;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Test\Integration\Fixture\OrderBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Sales\Model\Order;
use Magento\TestFramework\Helper\Bootstrap;
use PHPUnit\Framework\TestCase;
use TddWizard\Fixtures\Sales\OrderFixture;
use TddWizard\Fixtures\Sales\OrderFixtureRollback;

/**
 * Order export
 *
 * Assert that additional fee totals are contained in the totals
 * when reading the order, e.g. via REST API calls.
 *
 * @magentoAppIsolation enabled
 */
class OrderExportTest extends TestCase
{
    /**
     * @var Order
     */
    private static $order;

    /**
     * @var Order
     */
    private static $orderWithFee;

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
    public static function createOrder()
    {
        self::$order = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate')->build();

        /** @var OrderBuilder $orderBuilder */
        $orderBuilder = OrderBuilder::anOrder()->withShippingMethod('flatrate_flatrate');
        foreach (self::$additionalFees as $code => $value) {
            $orderBuilder = $orderBuilder->withAdditionalFee($code, $value);
        }

        self::$orderWithFee = $orderBuilder->build();
    }

    /**
     * @throws \Exception
     */
    public static function createOrderRollback()
    {
        try {
            OrderFixtureRollback::create()->execute(
                new OrderFixture(self::$order),
                new OrderFixture(self::$orderWithFee)
            );
        } catch (\Exception $exception) {
            $argv = $_SERVER['argv'] ?? [];
            if (in_array('--verbose', $argv, true)) {
                $message = sprintf("Error during rollback: %s%s", $exception->getMessage(), PHP_EOL);
                register_shutdown_function('fwrite', STDERR, $message);
            }
        }
    }

    /**
     * @param OrderInterface $order
     */
    private static function assertTotalsNotLoaded(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();

        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getDhlgwAdditionalFeeInclTax());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFee());
        self::assertEmpty($extensionAttributes->getBaseDhlgwAdditionalFeeInclTax());
    }

    /**
     * @param OrderInterface $order
     */
    private static function assertTotalsLoaded(OrderInterface $order)
    {
        $extensionAttributes = $order->getExtensionAttributes();

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
     * Assert `OrderRepository::get` behaviour.
     *
     * @test
     * @magentoDataFixture createOrder
     */
    public function getById()
    {
        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->create(OrderRepositoryInterface::class);

        $order = $orderRepository->get(self::$order->getId());
        self::assertTotalsNotLoaded($order);

        $order = $orderRepository->get(self::$orderWithFee->getId());
        self::assertTotalsLoaded($order);
    }

    /**
     * Assert `OrderRepository::getList` behaviour.
     *
     * @test
     * @magentoDataFixture createOrder
     */
    public function getList()
    {
        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->create(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter(
            OrderInterface::ENTITY_ID,
            [self::$order->getId(),  self::$orderWithFee->getId()],
            'in'
        );
        $searchCriteria = $searchCriteriaBuilder->create();

        /** @var OrderRepositoryInterface $orderRepository */
        $orderRepository = Bootstrap::getObjectManager()->create(OrderRepositoryInterface::class);
        $orders = $orderRepository->getList($searchCriteria)->getItems();

        self::assertNotEmpty($orders);
        foreach ($orders as $order) {
            $order->getEntityId() === self::$orderWithFee->getEntityId()
                ? self::assertTotalsLoaded($order)
                : self::assertTotalsNotLoaded($order);
        }
    }
}
