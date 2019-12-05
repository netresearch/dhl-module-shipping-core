<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ParcelshopFinder;

use Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface;
use Dhl\ShippingCore\Api\Data\ParcelshopFinder\LocationInterface;
use Dhl\ShippingCore\Api\Data\ParcelshopFinder\OpeningHoursInterface;

/**
 * Class Location
 *
 * @package Dhl\ShippingCore\Model\ParcelShopFinder
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
class Location implements LocationInterface
{
    /**
     * @var string
     */
    private $shopType;

    /**
     * @var string
     */
    private $shopNumber;

    /**
     * @var string
     */
    private $shopName;

    /**
     * @var string
     */
    private $shopId;

    /**
     * @var string[]
     */
    private $services;

    /**
     * @var AddressInterface
     */
    private $address;

    /**
     * @var OpeningHoursInterface[]
     */
    private $openingHours;

    /**
     * @var string
     */
    private $icon;

    /**
     * @var float
     */
    private $latitude;

    /**
     * @var float
     */
    private $longitude;

    /**
     * @var string
     */
    private $displayName;

    /**
     * @return string
     */
    public function getShopType(): string
    {
        return $this->shopType;
    }

    /**
     * @return string
     */
    public function getShopNumber(): string
    {
        return $this->shopNumber;
    }

    /**
     * @return string
     */
    public function getShopName(): string
    {
        return $this->shopName;
    }

    /**
     * @return string
     */
    public function getShopId(): string
    {
        return $this->shopId;
    }

    /**
     * @return string[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @return AddressInterface
     */
    public function getAddress(): AddressInterface
    {
        return $this->address;
    }

    /**
     * @return OpeningHoursInterface[]
     */
    public function getOpeningHours(): array
    {
        return $this->openingHours;
    }

    /**
     * @return string
     */
    public function getIcon(): string
    {
        return $this->icon;
    }

    /**
     * @return float
     */
    public function getLongitude(): float
    {
        return $this->longitude;
    }

    /**
     * @return float
     */
    public function getLatitude(): float
    {
        return $this->latitude;
    }
    /**
     * @return string
     */
    public function getDisplayName(): string
    {
        return $this->displayName;
    }

    /**
     * @param string $shopType
     */
    public function setShopType(string $shopType)
    {
        $this->shopType = $shopType;
    }

    /**
     * @param string $shopNumber
     */
    public function setShopNumber(string $shopNumber)
    {
        $this->shopNumber = $shopNumber;
    }

    /**
     * @param string $shopName
     */
    public function setShopName(string $shopName)
    {
        $this->shopName = $shopName;
    }

    /**
     * @param string $shopId
     */
    public function setShopId(string $shopId)
    {
        $this->shopId = $shopId;
    }

    /**
     * @param string[] $services
     */
    public function setServices(array $services)
    {
        $this->services = $services;
    }

    /**
     * @param AddressInterface $address
     */
    public function setAddress(AddressInterface $address)
    {
        $this->address = $address;
    }

    /**
     * @param OpeningHoursInterface[] $openingHours
     */
    public function setOpeningHours(array $openingHours)
    {
        $this->openingHours = $openingHours;
    }

    /**
     * @param string $icon
     */
    public function setIcon(string $icon)
    {
        $this->icon = $icon;
    }

    /**
     * @param float $latitude
     */
    public function setLatitude(float $latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * @param float $longitude
     */
    public function setLongitude(float $longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * @param string $displayName
     */
    public function setDisplayName(string $displayName)
    {
        $this->displayName = $displayName;
    }
}
