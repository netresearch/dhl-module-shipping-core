<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

/**
 * Interface CarrierDataInterface
 *
 * A DTO for carrier-specific data for rendering additional shipping options.
 *
 * @api
 */
interface CarrierDataInterface
{
    /**
     * The code of the carrier this set of data concerns
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Retrieve additional information to render the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface|null
     */
    public function getMetadata();

    /**
     * Retrieve rendering information about the shipping options the carrier offers on package level.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    public function getPackageOptions(): array;

    /**
     * Retrieve rendering information about the shipping options the carrier offers on item level.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface[]
     */
    public function getItemOptions(): array;

    /**
     * Retrieve rendering information about the value-added service shipping options the carrier offers.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    public function getServiceOptions(): array;

    /**
     * Retrieve compatibility data to handle user input into the shipping options at runtime.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface[]
     */
    public function getCompatibilityData(): array;

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface $metadata
     *
     * @return void
     */
    public function setMetadata(MetadataInterface $metadata);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $packageOptions
     *
     * @return void
     */
    public function setPackageOptions(array $packageOptions);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface[] $itemOptions
     *
     * @return void
     */
    public function setItemOptions(array $itemOptions);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $serviceOptions
     *
     * @return void
     */
    public function setServiceOptions(array $serviceOptions);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface[] $compatibilityData
     *
     * @return void
     */
    public function setCompatibilityData(array $compatibilityData);
}
