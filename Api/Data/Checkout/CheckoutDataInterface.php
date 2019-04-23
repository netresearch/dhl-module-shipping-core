<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface CheckoutDataInterface
 *
 * A DTO with shipping options rendering data for carriers that support it
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CheckoutDataInterface
{
    /**
     * Retrieve a list of carrier-specific data for rendering additional shipping options.
     *
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[]
     */
    public function getCarriers(): array;
}
