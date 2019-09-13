<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentRequest;

/**
 * Interface PackageAdditionalInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface PackageAdditionalInterface
{
    /**
     * Obtain additional (carrier-specific) package properties.
     *
     * @return mixed[]
     */
    public function getData(): array;
}
