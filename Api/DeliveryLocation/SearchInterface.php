<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\DeliveryLocation;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;

/**
 * @api
 */
interface SearchInterface
{
    /**
     * @param string $carrierCode
     * @param \Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface $searchAddress
     * @return \Dhl\ShippingCore\Api\Data\DeliveryLocation\LocationInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function search(string $carrierCode, AddressInterface $searchAddress): array;
}
