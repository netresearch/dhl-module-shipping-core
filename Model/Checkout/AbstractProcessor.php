<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingOptions\CheckoutProcessorInterface;

/**
 * Interface InternalProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @api
 */
class AbstractProcessor implements CheckoutProcessorInterface
{
    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        return $optionsData;
    }

    /**
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
    ): MetadataInterface {
        return $metadata;
    }

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return CompatibilityInterface[]
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        return $compatibilityData;
    }
}
