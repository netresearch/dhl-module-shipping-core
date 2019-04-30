<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Generator;

use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

class ShipmentRequestData
{
    /**
     * Generates a plain POST request for the shipment save controller with only the core data
     *
     * @param OrderInterface $order
     * @return array
     */
    public static function generatePostData(OrderInterface $order): array
    {
        $shipment = [
            'items' => [],
            'create_shipping_label' => '1',
        ];

        $packageWeight = 0;
        $packageValue = 0;
        $package = [
            'params' => [],
            'items' => [],
        ];

        /** @var OrderItemInterface $item */
        foreach ($order->getItems() as $item) {
            $shipment['items'][$item->getItemId()] = $item->getQtyOrdered();

            $package['items'][$item->getItemId()] = [
                'qty' => $item->getQtyOrdered(),
                'customs_value' => $item->getBasePrice(),
                'price' => $item->getBasePrice(),
                'name' => $item->getName(),
                'weight' => $item->getWeight(),
                'product_id' => $item->getProductId(),
                'order_item_id' => $item->getItemId(),
            ];

            $packageWeight += $item->getRowWeight();
            $packageValue += $item->getRowTotalInclTax();
        }

        $package['params'] = [
            'weight' => $packageWeight,
            'customs_value' => $packageValue,
            'length' => '30.0',
            'width' => '20.0',
            'height' => '20.0',
            'weight_units' => \Zend_Measure_Weight::KILOGRAM,
            'dimension_units' => \Zend_Measure_Length::CENTIMETER,
            'content_type' => '',
            'content_type_other' => '',
        ];

        $postData = [
            'shipment' => $shipment,
            'packages' => ['1' => $package],
        ];

        return $postData;
    }
}
