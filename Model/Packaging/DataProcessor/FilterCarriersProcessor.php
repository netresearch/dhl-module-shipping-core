<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Packaging\PackagingArrayProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class FilterCarriersProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class FilterCarriersProcessor implements PackagingArrayProcessorInterface
{
    /**
     * Remove all carrier data that does not match the given shipment.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, Shipment $shipment): array
    {
        $orderCarrier = strtok((string)$shipment->getOrder()->getShippingMethod(), '_');
        $shippingData['carriers'] = array_filter(
            $shippingData['carriers'],
            function (array $carrier) use ($orderCarrier) {
                return $carrier['code'] === $orderCarrier;
            }
        );
        return $shippingData;
    }
}
