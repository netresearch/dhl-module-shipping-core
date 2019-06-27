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
     * Additional custom data.
     *
     * @var array
     */
    private $customs;

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
     * @param array $customs
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
        array $customs = []
    ) {
        $this->orderItemId = $orderItemId;
        $this->productId = $productId;
        $this->packageId = $packageId;
        $this->name = $name;
        $this->qty = $qty;
        $this->weight = $weight;
        $this->price = $price;
        $this->customsValue = $customsValue;
        $this->customs = $customs;
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
     * Obtain item's additional customs data.
     *
     * @return array
     */
    public function getCustoms(): array
    {
        return $this->customs;
    }

    /**
     * Obtain item's HS code.
     *
     * @return null|string
     */
    public function getHsCode()
    {
        return $this->customs['hsCode'] ?? null;
    }

    /**
     * Obtain item's export description.
     *
     * @return string
     */
    public function getExportDescription(): string
    {
        return $this->customs['exportDescription'] ?? '';
    }

    /**
     * Obtain item's dangerous goods category.
     *
     * @return null|string
     */
    public function getDangerousGoodsCategory()
    {
        return $this->customs['dgCategory'] ?? null;
    }
}
