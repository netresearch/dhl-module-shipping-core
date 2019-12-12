<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\Util\ItemAttributeReaderInterface;
use Dhl\ShippingCore\Model\Util\ShipmentItemFilter;
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
     * @var ItemAttributeReaderInterface
     */
    private $orderItemAttributeReader;

    /**
     * @var ShipmentItemFilter
     */
    private $itemFilter;

    /**
     * ShipmentItemAttributeReader constructor.
     *
     * @param ItemAttributeReaderInterface $orderItemAttributeReader
     * @param ShipmentItemFilter $itemFilter
     */
    public function __construct(
        ItemAttributeReaderInterface $orderItemAttributeReader,
        ShipmentItemFilter $itemFilter
    ) {
        $this->orderItemAttributeReader = $orderItemAttributeReader;
        $this->itemFilter = $itemFilter;
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
            $totalWeight += $shipmentItem->getWeight() * $shipmentItem->getQty();
            return $totalWeight;
        };

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return (float) array_reduce($items, $fnAdd, 0);
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
            $totalAmount = $shipmentItem->getOrderItem()->getBaseRowTotal()
                - $shipmentItem->getOrderItem()->getBaseDiscountAmount()
                + $shipmentItem->getOrderItem()->getBaseTaxAmount()
                + $shipmentItem->getOrderItem()->getbaseDiscountTaxCompensationAmount();

            $itemPrice = $totalAmount / $shipmentItem->getOrderItem()->getQtyOrdered();
            $price += ($itemPrice * $shipmentItem->getQty());

            return $price;
        };

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return (float) array_reduce($items, $fnAdd, 0);
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

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        return array_map($fnCollect, $items);
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

        $items = $this->itemFilter->getShippableItems($shipment->getAllItems());
        $dgCategories = array_map($fnCollect, $items);

        return array_filter($dgCategories);
    }
}
