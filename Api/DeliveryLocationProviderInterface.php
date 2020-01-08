<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface;

/**
 * Interface LocationFinderManagementInterface
 *
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface DeliveryLocationProviderInterface
{
    /**
     * @param string $carrierCode
     * @param \Dhl\ShippingCore\Api\Data\DeliveryLocation\AddressInterface $searchAddress
     * @return \Dhl\ShippingCore\Api\Data\DeliveryLocation\LocationInterface[]
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function search(string $carrierCode, AddressInterface $searchAddress): array;
}
