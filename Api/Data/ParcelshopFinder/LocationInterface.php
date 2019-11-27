<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ParcelshopFinder;

/**
 * Interface LocationInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface LocationInterface
{
    /**
     * @return string
     */
    public function getShopType(): string;

    /**
     * @return string
     */
    public function getShopNumber(): string;

    /**
     * @return string
     */
    public function getShopName(): string;

    /**
     * @return string
     */
    public function getShopId(): string;

    /**
     * @return \Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface
     */
    public function getAddress(): AddressInterface;

    /**
     * @return \Dhl\ShippingCore\Api\Data\ParcelshopFinder\OpeningHoursInterface[]
     */
    public function getOpeningHours(): array;

    /**
     * @return string
     */
    public function getIcon(): string;

    /**
     * @return float
     */
    public function getLongitude(): float;

    /**
     * @return float
     */
    public function getLatitude(): float;

    /**
     * @param string $shopType
     * @return void
     */
    public function setShopType(string $shopType);

    /**
     * @param string $shopNumber
     * @return void
     */
    public function setShopNumber(string $shopNumber);

    /**
     * @param string $shopName
     * @return void
     */
    public function setShopName(string $shopName);

    /**
     * @param string $shopId
     * @return void
     */
    public function setShopId(string $shopId);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface $address
     * @return void
     */
    public function setAddress(AddressInterface $address);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ParcelshopFinder\OpeningHoursInterface[] $openingHours
     * @return void
     */
    public function setOpeningHours(array $openingHours);

    /**
     * @param string $icon
     * @return void
     */
    public function setIcon(string $icon);

    /**
     * @param float $latitude
     * @return void
     */
    public function setLatitude(float $latitude);

    /**
     * @param float $longitude
     * @return void
     */
    public function setLongitude(float $longitude);
}
