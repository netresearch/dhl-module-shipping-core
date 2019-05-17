<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface CarrierDataInterface
 *
 * A DTO for carrier-specific data for rendering additional shipping options.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CarrierDataInterface
{
    /**
     * The code of the carrier this set of data concerns
     *
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * Retrieve rendering information about the shipping options the carrier offers on package level.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    public function getPackageLevelOptions(): array;

    /**
     * Retrieve rendering information about the shipping options the carrier offers on item level.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    public function getItemLevelOptions(): array;

    /**
     * Retrieve compatibility data to handle user input into the shipping options at runtime.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[]
     */
    public function getCompatibilityData(): array;

    /**
     * Retrieve additional information to render the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\Checkout\MetadataInterface
     */
    public function getMetadata(): MetadataInterface;
}
