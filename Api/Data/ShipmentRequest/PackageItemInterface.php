<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentRequest;

/**
 * Interface PackageItemInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface PackageItemInterface
{
    /**
     * Obtain order item id for the current item.
     *
     * @return int
     */
    public function getOrderItemId(): int;

    /**
     * Obtain product id for the current item.
     *
     * @return int
     */
    public function getProductId(): int;

    /**
     * Obtain package id the current item is packed in.
     *
     * @return int
     */
    public function getPackageId(): int;

    /**
     * Obtain product name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Obtain item qty (can be decimal value, e.g. 1.3 liters).
     *
     * @return float
     */
    public function getQty(): float;

    /**
     * Obtain item weight.
     *
     * @return float
     */
    public function getWeight(): float;

    /**
     * Obtain item price.
     *
     * @return float
     */
    public function getPrice(): float;

    /**
     * Obtain item's custom value (optional).
     *
     * @return float|null
     */
    public function getCustomsValue();

    /**
     * Obtain item's additional customs data.
     *
     * @return array
     */
    public function getCustoms(): array;

    /**
     * Obtain item's HS code.
     *
     * @return null|string
     */
    public function getHsCode();

    /**
     * Obtain item's export description.
     *
     * @return string
     */
    public function getExportDescription(): string;

    /**
     * Obtain item's dangerous goods category.
     *
     * @return null|string
     */
    public function getDangerousGoodsCategory();
}
