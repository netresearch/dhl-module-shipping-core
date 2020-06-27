<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\DeliveryLocation;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;
use Dhl\ShippingCore\Api\Data\DeliveryLocation\LocationInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * @api
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
