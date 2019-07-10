<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * Class ItemAttributeReader
 *
 * Read product attributes from shipment items.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ItemAttributeReader
{
    /**
     * Obtain the actual product added to cart, i.e. the chosen configuration, and return value of given attribute.
     *
     * @param Item $shipmentItem
     * @param string $attributeCode
     * @return string
     */
    private function readAttribute(Item $shipmentItem, string $attributeCode): string
    {
        $orderItem = $shipmentItem->getOrderItem();

        // load the product to read the attribute from. if configurable item is passed in, load from simple item.
        if ($orderItem->getProductType() === Configurable::TYPE_CODE) {
            $childItem = current($orderItem->getChildrenItems());
            $product = $childItem->getProduct();
        } else {
            $product = $orderItem->getProduct();
        }

        if (!$product) {
            return '';
        }

        if ($product->hasData($attributeCode)) {
            // attribute value found in simple
            return (string) $product->getData($attributeCode);
        }

        // as a last resort, fall back to the configurable (if exists) or return empty value
        return (string) ($orderItem->getProduct() ? $orderItem->getProduct()->getData($attributeCode) : '');
    }

    /**
     * Read weight from product.
     *
     * @param Item $shipmentItem
     * @return float
     */
    public function getWeight(Item $shipmentItem): float
    {
        return (float) $this->readAttribute($shipmentItem, 'weight');
    }

    /**
     * Read HS code from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getHsCode(Item $shipmentItem): string
    {
        return $this->readAttribute($shipmentItem, TariffNumber::CODE);
    }

    /**
     * Read dangerous goods category from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getDgCategory(Item $shipmentItem): string
    {
        return $this->readAttribute($shipmentItem, DGCategory::CODE);
    }

    /**
     * Read export description from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getExportDescription(Item $shipmentItem): string
    {
        return $this->readAttribute($shipmentItem, ExportDescription::CODE);
    }

    /**
     * Read country of manufacture from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getCountryOfManufacture(Item $shipmentItem): string
    {
        return $this->readAttribute($shipmentItem, 'country_of_manufacture');
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
