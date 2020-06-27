<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor;

use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsArrayProcessorInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

class FilterCarriersProcessor implements ShippingOptionsArrayProcessorInterface
{
    /**
     * Remove all carrier data that does not match the given shipment.
     *
     * @param mixed[] $shippingData
     * @param ShipmentInterface $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, ShipmentInterface $shipment): array
    {
        /** @var \Magento\Sales\Model\Order $order */
        $order = $shipment->getOrder();
        $orderCarrier = strtok((string) $order->getShippingMethod(), '_');

        $shippingData['carriers'] = array_filter(
            $shippingData['carriers'],
            static function (array $carrier) use ($orderCarrier) {
                return $carrier['code'] === $orderCarrier;
            }
        );

        return $shippingData;
    }
}
