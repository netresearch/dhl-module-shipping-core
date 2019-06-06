<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

/**
 * Interface ShippingDataInterface
 *
 * A DTO with shipping options rendering data for carriers that support it
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ShippingDataInterface
{
    /**
     * Retrieve a list of carrier-specific data for rendering additional shipping options.
     *
     * @return \Dhl\ShippingCore\Api\Data\CarrierDataInterface[]
     */
    public function getCarriers(): array;

    /**
     * @param \Dhl\ShippingCore\Api\Data\CarrierDataInterface[] $carriers
     *
     * @return void
     */
    public function setCarriers(array $carriers);
}
