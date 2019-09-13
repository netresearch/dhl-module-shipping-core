<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest;

use Dhl\ShippingCore\Api\Data\ShipmentRequest\PackageAdditionalInterface;

/**
 * Class PackageAdditional
 *
 * @package Dhl\ShippingCore\Model
 */
class PackageAdditional implements PackageAdditionalInterface
{
    /**
     * Obtain additional (carrier-specific) package properties.
     *
     * @return mixed[]
     */
    public function getData(): array
    {
        return [];
    }
}
