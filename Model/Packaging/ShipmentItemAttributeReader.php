<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Model\Order\ItemAttributeReader;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * Class ShipmentItemAttributeReader
 *
 * Read additional attributes from shipment items.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShipmentItemAttributeReader
{
    /**
     * @var ItemAttributeReader
     */
    private $orderItemAttributeReader;

    /**
     * ShipmentItemAttributeReader constructor.
     *
     * @param ItemAttributeReader $orderItemAttributeReader
     */
    public function __construct(ItemAttributeReader $orderItemAttributeReader)
    {
        $this->orderItemAttributeReader = $orderItemAttributeReader;
    }

    /**
     * Read weight from shipment item.
     *
     * @param Item $shipmentItem
     * @return float
     */
    public function getWeight(Item $shipmentItem): float
    {
        return (float) $shipmentItem->getWeight();
    }

    /**
     * Read HS code from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getHsCode(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getHsCode($orderItem);
    }

    /**
     * Read dangerous goods category from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getDgCategory(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getDgCategory($orderItem);
    }

    /**
     * Read export description from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getExportDescription(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getExportDescription($orderItem);
    }

    /**
     * Read country of manufacture from extension attributes.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getCountryOfManufacture(Item $shipmentItem): string
    {
        $orderItem = $shipmentItem->getOrderItem();
        return $this->orderItemAttributeReader->getCountryOfManufacture($orderItem);
    }

    /**
     * Add together all items' weight multiplied by quantity to ship.
     *
     * @param Shipment $shipment
     * @return float
     */
    public function getTotalWeight(Shipment $shipment): float
    {
        $fnAdd = function ($totalWeight, Item $shipmentItem) {
            $itemWeight = $this->getWeight($shipmentItem);

            $totalWeight += $itemWeight * $shipmentItem->getQty();
            return $totalWeight;
        };

        return array_reduce($shipment->getAllItems(), $fnAdd, 0);
    }

    /**
     * Add together all items' price multiplied by quantity to ship.
     *
     * @param Shipment $shipment
     * @return float
     */
    public function getTotalPrice(Shipment $shipment): float
    {
        $fnAdd = function ($price, Item $shipmentItem) {
            $price += $shipmentItem->getPrice() * $shipmentItem->getQty();
            return $price;
        };

        return array_reduce($shipment->getAllItems(), $fnAdd, 0);
    }

    /**
     * Obtain all items' export description.
     *
     * @param Shipment $shipment
     * @return string[]
     */
    public function getPackageExportDescriptions(Shipment $shipment): array
    {
        $fnCollect = function (Item $shipmentItem) {
            $itemExportDescription = $this->getExportDescription($shipmentItem);
            if ($itemExportDescription) {
                return $itemExportDescription;
            }

            if ($shipmentItem->getDescription()) {
                return $shipmentItem->getDescription();
            }

            return $shipmentItem->getName();
        };

        return array_map($fnCollect, $shipment->getAllItems());
    }

    /**
     * Obtain all items' dangerous goods categories.
     *
     * @param Shipment $shipment
     * @return string[]
     */
    public function getPackageDgCategories(Shipment $shipment): array
    {
        $fnCollect = function (Item $shipmentItem) {
            return $this->getDgCategory($shipmentItem);
        };

        $dgCategories = array_map($fnCollect, $shipment->getAllItems());

        return array_filter($dgCategories);
    }
}
