<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Packaging\PackagingArrayProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class CloneItemTemplatesProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CloneItemTemplatesProcessor implements PackagingArrayProcessorInterface
{
    /**
     * Convert the static ItemShippingOption arrays read from xml
     * into separate elements for each shipment item.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, Shipment $shipment): array
    {
        foreach ($shippingData['carriers'] as $carrierCode => $carrier) {
            $newData = [];
            foreach ($shipment->getItems() as $item) {
                $itemId = (int)$item->getOrderItemId();
                $newItem = [
                    'itemId' => $itemId,
                    'shippingOptions' => [],
                ];
                foreach ($carrier['itemOptions'] as $itemOptions) {
                    $newItem['shippingOptions'] += $itemOptions['shippingOptions'];
                }
                $newData[$itemId] = $newItem;
            }
            $shippingData['carriers'][$carrierCode]['itemOptions'] = $newData;
        }

        return $shippingData;
    }
}
