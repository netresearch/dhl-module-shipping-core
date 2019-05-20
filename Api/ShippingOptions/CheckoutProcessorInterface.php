<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\ShippingOptions;

/**
 * Interface CheckoutProcessorInterface
 *
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface CheckoutProcessorInterface
{
    /**
     * Receive an array of shipping option items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param mixed[] $optionsData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return mixed[]
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array;

    /**
     * Receive shipping option metadata, modify it according to business logic and return the modified array.
     *
     * @param array $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return array
     */
    public function processMetadata(
        array $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array;

    /**
     * Receive an array of compatibility rule data items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param array $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return array
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array;
}
