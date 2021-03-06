<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\SplitAddress;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * @api
 */
interface RecipientStreetLoaderInterface
{
    /**
     * Load or create a split address by given order address.
     *
     * @param OrderAddressInterface $address
     * @return RecipientStreetInterface
     */
    public function load(OrderAddressInterface $address): RecipientStreetInterface;
}
