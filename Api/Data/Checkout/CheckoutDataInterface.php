<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface CheckoutDataInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface CheckoutDataInterface
{
    /**
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[];
     */
    public function getCarriers(): array;
}
