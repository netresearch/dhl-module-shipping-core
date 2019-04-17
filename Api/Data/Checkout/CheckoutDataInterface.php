<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface CheckoutDataInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CheckoutDataInterface
{
    /**
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[]
     */
    public function getCarriers(): array;
}
