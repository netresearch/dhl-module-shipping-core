<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\ParcelshopFinder\AddressInterface;
use Dhl\ShippingCore\Api\Data\ParcelshopFinder\LocationInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Interface LocationProviderInterface
 *
 * @package Dhl\ShippingCore\Api
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface LocationProviderInterface
{
    /**
     * @param AddressInterface $address
     * @return LocationInterface[]
     * @throws LocalizedException
     */
    public function getLocationsByAddress(AddressInterface $address): array;

    /**
     * @return string
     */
    public function getCarrierCode():string;
}
