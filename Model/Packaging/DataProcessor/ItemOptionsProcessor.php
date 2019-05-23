<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
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
        if ($optionGroupName !== 'itemLevelOptions') {
            return $optionsData;
        }

        foreach ($optionsData as $optionCode => $option) {
            /** Clone template option for every shipment item. */
            foreach ($shipment->getItems() as $item) {
                $itemId = $item->getOrderItemId();
                $clonedOption = $option;
                $clonedOption['itemId'] = $itemId;
                $optionsData["$optionCode.$itemId"] = $clonedOption;
            }
            /** Remove template option */
            unset($optionsData[$optionCode]);
        }

        return $optionsData;
    }
}
