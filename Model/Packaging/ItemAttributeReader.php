<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * Class ItemAttributeReader
 *
 * Read properties from shipment items. If a configurable is passed in, properties are read from the actual simple.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ItemAttributeReader
{
    /**
     * Obtain the actual product added to cart, i.e. the chosen configuration.
     *
     * @param Item $shipmentItem
     * @return Product
     */
    private static function getProductFromShipmentItem(Item $shipmentItem)
    {
        $orderItem = $shipmentItem->getOrderItem();
        if ($orderItem->getProductType() === Configurable::TYPE_CODE) {
            $childItem = current($orderItem->getChildrenItems());
            $product = $childItem->getProduct();
        } else {
            $product = $orderItem->getProduct();
        }

        return $product;
    }

    /**
     * Read weight from product.
     *
     * @param Item $shipmentItem
     * @return float
     */
    public function getWeight(Item $shipmentItem): float
    {
        $product = self::getProductFromShipmentItem($shipmentItem);
        $weight = $product->getWeight();

        return (float) ($weight ?: $shipmentItem->getOrderItem()->getProduct()->getWeight());
    }

    /**
     * Read HS code from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getHsCode(Item $shipmentItem)
    {
        $product = self::getProductFromShipmentItem($shipmentItem);
        if ($product->hasData(TariffNumber::CODE)) {
            return (string) $product->getData(TariffNumber::CODE);
        }

        return (string) $shipmentItem->getOrderItem()->getProduct()->getData(TariffNumber::CODE);
    }

    /**
     * Read dangerous goods category from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getDgCategory(Item $shipmentItem): string
    {
        $product = self::getProductFromShipmentItem($shipmentItem);
        if ($product->hasData(DGCategory::CODE)) {
            return (string) $product->getData(DGCategory::CODE);
        }

        return (string) $shipmentItem->getOrderItem()->getProduct()->getData(DGCategory::CODE);
    }

    /**
     * Read export description from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getExportDescription(Item $shipmentItem): string
    {
        $product = self::getProductFromShipmentItem($shipmentItem);

        if ($product->hasData(ExportDescription::CODE)) {
            return (string) $product->getData(ExportDescription::CODE);
        }

        return (string) $shipmentItem->getOrderItem()->getProduct()->getData(ExportDescription::CODE);
    }

    /**
     * Read country of manufacture from product.
     *
     * @param Item $shipmentItem
     * @return string
     */
    public function getCountryOfManufacture(Item $shipmentItem): string
    {
        $product = self::getProductFromShipmentItem($shipmentItem);

        if ($product->hasData('country_of_manufacture')) {
            return (string) $product->getData('country_of_manufacture');
        }

        return (string) $shipmentItem->getOrderItem()->getProduct()->getData('country_of_manufacture');
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
