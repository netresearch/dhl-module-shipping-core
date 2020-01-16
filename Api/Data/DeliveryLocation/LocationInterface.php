<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\DeliveryLocation;

/**
 * Interface LocationInterface
 *
 * @api
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
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
    public function getShopId(): string;

    /**
     * @return string[]
     */
    public function getServices(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface
     */
    public function getAddress(): AddressInterface;

    /**
     * @return \Dhl\ShippingCore\Api\Data\DeliveryLocation\OpeningHoursInterface[]
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
     * @return string
     */
    public function getDisplayName(): string;

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
     * @param string $shopId
     * @return void
     */
    public function setShopId(string $shopId);

    /**
     * @param string[] $services
     * @return void
     */
    public function setServices(array $services);

    /**
     * @param \Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface $address
     * @return void
     */
    public function setAddress(AddressInterface $address);

    /**
     * @param \Dhl\ShippingCore\Api\Data\DeliveryLocation\OpeningHoursInterface[] $openingHours
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

    /**
     * @param string $displayName
     * @return void
     */
    public function setDisplayName(string $displayName);
}
