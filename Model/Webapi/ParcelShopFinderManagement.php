<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Webapi;

use Dhl\ShippingCore\Api\Data\ParcelshopFinder\LocationInterface;
use Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface;
use Dhl\ShippingCore\Api\LocationProviderInterface;
use Dhl\ShippingCore\Api\ParcelShopFinderManagementInterface;

/**
 * Class ParcelShopFinderManagement
 *
 * @package Dhl\ShippingCore\Model\Webapi
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
class ParcelShopFinderManagement implements ParcelShopFinderManagementInterface
{
    /**
     * @var LocationProviderInterface[]
     */
    private $locationProviders;

    /**
     * ParcelShopFinderManagement constructor.
     *
     * @param LocationProviderInterface[] $locationProviders
     */
    public function __construct($locationProviders = [])
    {
        $this->locationProviders = $locationProviders;
    }

    /**
     * @param string $carrierCode
     * @param AddressInterface $address
     * @return LocationInterface[]
     */
    public function getLocationByAddress(string $carrierCode, AddressInterface $address): array
    {
        foreach ($this->locationProviders as $provider) {
            if ($provider->getCarrierCode() === $carrierCode) {
                return $provider->getLocationsByAddress($address);
            }
        }

        return [];
    }
}
