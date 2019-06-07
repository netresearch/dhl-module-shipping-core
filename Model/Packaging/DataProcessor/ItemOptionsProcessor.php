<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterfaceFactory;
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
    /**
     * @var ItemShippingOptionsInterfaceFactory
     */
    private $itemsShippingOptionsFactory;

    /**
     * ItemOptionsProcessor constructor.
     *
     * @param ItemShippingOptionsInterfaceFactory $itemsShippingOptionsFactory
     */
    public function __construct(ItemShippingOptionsInterfaceFactory $itemsShippingOptionsFactory)
    {
        $this->itemsShippingOptionsFactory = $itemsShippingOptionsFactory;
    }

    /**
     * Convert the static ItemShippingOption array read from xml (with itemId 0)
     * into separate objects for each shipment item.
     *
     * This processor must run before for other item processors
     * for them to work correctly.
     *
     * @param ItemShippingOptionsInterface[] $itemOptionsData
     * @param Shipment $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(array $itemOptionsData, Shipment $shipment): array
    {
        /** @var ItemShippingOptionsInterface[] $newData */
        $newData = [];
        foreach ($shipment->getItems() as $item) {
            $newItem = $this->itemsShippingOptionsFactory->create();
            $newItem->setItemId((int)$item->getOrderItemId());
            foreach ($itemOptionsData as $index => $itemOptions) {
                $newItem->setShippingOptions(
                    $newItem->getShippingOptions() + $itemOptions->getShippingOptions()
                );
                unset($itemOptionsData[$index]);
            }
            $newData[] = $newItem;
        }
        $itemOptionsData += $newData;

        return $itemOptionsData;
    }
}
