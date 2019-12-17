<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface;

/**
 * Interface LocationFinderManagementInterface
 *
 * @package Dhl\ShippingCore\Api
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface ParcelShopFinderManagementInterface
{
    /**
     * @param string $carrierCode
     * @param \Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface $address
     * @return \Dhl\ShippingCore\Api\Data\ParcelshopFinder\LocationInterface[]
     */
    public function getLocationByAddress(string $carrierCode, AddressInterface $address): array;
}
