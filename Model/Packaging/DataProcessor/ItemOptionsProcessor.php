<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ItemOptionsProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemOptionsProcessor extends AbstractProcessor
{
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_ITEM) {
            return $optionsData;
        }

        $newOptionsData = [];
        foreach ($shipment->getItems() as $item) {
            $itemId = $item->getOrderItemId();
            foreach ($optionsData as $optionCode => $option) {
                /** Clone template option for every shipment item. */
                if (!isset($newOptionsData[$itemId])) {
                    $newOptionsData[$itemId] = [
                        'itemId' => (int)$itemId,
                        'shippingOptions' => [$optionCode => $option]
                    ];
                } else {
                    $newOptionsData[$itemId]['shippingOptions'][$optionCode] = $option;
                }
            }
        }

        return $newOptionsData;
    }
}
