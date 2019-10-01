<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest;

use Dhl\ShippingCore\Api\Data\ShipmentRequest\PackageItemInterface;

/**
 * Class PackageItem
 *
 * @package Dhl\ShippingCore\Model
 */
class PackageItem implements PackageItemInterface
{
    /**
     * @var int
     */
    private $orderItemId;

    /**
     * @var int
     */
    private $productId;

    /**
     * @var int
     */
    private $packageId;

    /**
     * @var string
     */
    private $name;

    /**
     * @var float
     */
    private $qty;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var float
     */
    private $price;

    /**
     * @var float|null
     */
    private $customsValue;

    /**
     * @var string
     */
    private $sku;

    /**
     * @var string
     */
    private $exportDescription;

    /**
     * @var string
     */
    private $hsCode;

    /**
     * @var string
     */
    private $countryOfOrigin;

    /**
     * PackageItem constructor.
     * @param int $orderItemId
     * @param int $productId
     * @param int $packageId
     * @param string $name
     * @param float $qty
     * @param float $weight
     * @param float $price
     * @param float|null $customsValue
     * @param string $sku
     * @param string $exportDescription
     * @param string $hsCode
     * @param string $countryOfOrigin
     */
    public function __construct(
        int $orderItemId,
        int $productId,
        int $packageId,
        string $name,
        float $qty,
        float $weight,
        float $price,
        float $customsValue = null,
        string $sku = '',
        string $exportDescription = '',
        string $hsCode = '',
        string $countryOfOrigin = ''
    ) {
        $this->orderItemId = $orderItemId;
        $this->productId = $productId;
        $this->packageId = $packageId;
        $this->name = $name;
        $this->qty = $qty;
        $this->weight = $weight;
        $this->price = $price;
        $this->customsValue = $customsValue;
        $this->sku = $sku;
        $this->exportDescription = $exportDescription;
        $this->hsCode = $hsCode;
        $this->countryOfOrigin = $countryOfOrigin;
    }

    /**
     * Obtain order item id for the current item.
     *
     * @return int
     */
    public function getOrderItemId(): int
    {
        return $this->orderItemId;
    }

    /**
     * Obtain product id for the current item.
     *
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * Obtain package id the current item is packed in.
     *
     * @return int
     */
    public function getPackageId(): int
    {
        return $this->packageId;
    }

    /**
     * Obtain product name.
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Obtain item qty (can be decimal value, e.g. 1.3 liters).
     *
     * @return float
     */
    public function getQty(): float
    {
        return $this->qty;
    }

    /**
     * Obtain item weight.
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * Obtain item price.
     *
     * @return float
     */
    public function getPrice(): float
    {
        return $this->price;
    }

    /**
     * Obtain item's custom value (optional).
     *
     * @return float|null
     */
    public function getCustomsValue()
    {
        return $this->customsValue;
    }

    /**
     * Obtain item's SKU.
     *
     * @return string
     */
    public function getSku(): string
    {
        return $this->sku;
    }

    /**
     * Obtain item's custom declaration description.
     *
     * @return string
     */
    public function getExportDescription(): string
    {
        return $this->exportDescription;
    }

    /**
     * Obtain item's HS code / tariff number (optional).
     *
     * @return string
     */
    public function getHsCode(): string
    {
        return $this->hsCode;
    }

    /**
     * Obtain item's country of origin (optional).
     *
     * @return string
     */
    public function getCountryOfOrigin(): string
    {
        return $this->countryOfOrigin;
    }
}
