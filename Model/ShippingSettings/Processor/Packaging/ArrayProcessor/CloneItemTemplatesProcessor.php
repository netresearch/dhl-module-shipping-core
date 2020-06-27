<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor;

use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsArrayProcessorInterface;
use Dhl\ShippingCore\Model\Util\ShipmentItemFilter;
use Magento\Sales\Api\Data\ShipmentInterface;

class CloneItemTemplatesProcessor implements ShippingOptionsArrayProcessorInterface
{
    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    /**
     * CloneItemTemplatesProcessor constructor.
     *
     * @param ShipmentItemFilter $itemFilter
     */
    public function __construct(ShipmentItemFilter $itemFilter)
    {
        $this->itemFilter = $itemFilter;
    }

    /**
     * Convert the static ItemShippingOption arrays read from xml
     * into separate elements for each shipment item.
     *
     * @param mixed[] $shippingData
     * @param ShipmentInterface $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, ShipmentInterface $shipment): array
    {
        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());

        $itemOptions = [];
        foreach ($items as $item) {
            $itemId = (int)$item->getOrderItemId();
            $itemOptions[$itemId] = [
                'itemId' => $itemId,
                'shippingOptions' => [],
            ];
        }

        foreach ($shippingData['carriers'] as $carrierCode => &$carrier) {
            if (isset($carrier['itemOptions']) && is_array($carrier['itemOptions'])) {
                // add carrier's shipping options to item's shipping options
                foreach ($carrier['itemOptions'] as $carrierItemOption) {
                    foreach ($itemOptions as &$itemOption) {
                        $itemOption['shippingOptions'] += $carrierItemOption['shippingOptions'];
                    }
                }
            }

            $carrier['itemOptions'] = $itemOptions;
        }

        return $shippingData;
    }
}
