<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface CarrierDataInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CarrierDataInterface
{
    /**
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Checkout\ServiceMetadataInterface
     */
    public function getServiceMetadata(): ServiceMetadataInterface;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Selection\ServiceInterface[]
     */
    public function getServiceData(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Selection\CompatibilityInterface[]
     */
    public function getServiceCompatibilityData(): array;
}
