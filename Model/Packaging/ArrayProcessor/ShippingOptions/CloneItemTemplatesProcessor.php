<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\ArrayProcessor\ShippingOptions;

use Dhl\ShippingCore\Api\ShipmentItemFilterInterface;
use Dhl\ShippingCore\Model\Packaging\ArrayProcessor\ShippingOptionsProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class CloneItemTemplatesProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CloneItemTemplatesProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var ShipmentItemFilterInterface
     */
    private $itemFilter;

    /**
     * CloneItemTemplatesProcessor constructor.
     *
     * @param ShipmentItemFilterInterface $itemFilter
     */
    public function __construct(ShipmentItemFilterInterface $itemFilter)
    {
        $this->itemFilter = $itemFilter;
    }

    /**
     * Convert the static ItemShippingOption arrays read from xml
     * into separate elements for each shipment item.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, Shipment $shipment): array
    {
        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());

        foreach ($shippingData['carriers'] as $carrierCode => $carrier) {
            $newData = [];

            foreach ($items as $item) {
                $itemId = (int) $item->getOrderItemId();
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
