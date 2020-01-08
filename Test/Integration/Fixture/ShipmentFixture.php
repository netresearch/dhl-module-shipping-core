<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressInterface;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\ProductInterface;
use Magento\Sales\Model\Convert\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ShipmentFixture
 *
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShipmentFixture
{
    private static $createdEntities = [
        'products' => [],
        'customers' => [],
        'orders' => [],
    ];

    /**
     * @param AddressInterface $recipientData
     * @param ProductInterface[] $productData
     * @param string $carrierCode
     * @return Shipment
     * @throws \Exception
     */
    public static function createShipment(
        AddressInterface $recipientData,
        array $productData,
        string $carrierCode
    ): Shipment {
        /** @var \Magento\Sales\Model\Order $order */
        $order = OrderFixture::createOrder($recipientData, $productData, $carrierCode);

        /** @var Order $convertOrder */
        $convertOrder = Bootstrap::getObjectManager()->create(Order::class);
        $shipment = $convertOrder->toShipment($order);

        foreach ($order->getAllItems() as $orderItem) {
            // Check if order item has qty to ship or is virtual
            if (!$orderItem->getQtyToShip() || $orderItem->getIsVirtual()) {
                continue;
            }

            $qtyShipped = $orderItem->getQtyToShip();

            // Create shipment item with qty
            $shipmentItem = $convertOrder->itemToShipmentItem($orderItem)->setQty($qtyShipped);

            // Add shipment item to shipment
            $shipment->addItem($shipmentItem);
        }
        $shipment->register();

        return $shipment;
    }

    /**
     * Rollback for created order, customer and product entities
     *
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\StateException
     */
    public static function rollbackFixtureEntities()
    {
        OrderFixture::rollbackFixtureEntities();
    }
}
