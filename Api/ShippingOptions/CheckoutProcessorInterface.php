<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingOptions;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;

/**
 * Interface CheckoutProcessorInterface
 *
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface CheckoutProcessorInterface
{
    /**
     * Receive an array of shipping option items and
     * modify them according to business logic
     *
     * @param ShippingOptionInterface[] $shippingOptions
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return array
     */
    public function processShippingOptions(
        array $shippingOptions,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array;

    /**
     * Receive shipping option metadata and modify it according to business logic
     *
     * @param MetadataInterface $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): MetadataInterface;

    /**
     * Receive an array of compatibility rule data items and
     * modify them according to business logic
     *
     * @param CompatibilityInterface[] $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return array
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array;
}
