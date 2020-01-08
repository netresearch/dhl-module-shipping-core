<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor;

use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsArrayProcessorInterface;
use Dhl\ShippingCore\Model\Util\ShipmentItemFilter;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class CloneItemTemplatesProcessor
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
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
