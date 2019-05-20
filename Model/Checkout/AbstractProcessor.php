<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

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
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        return $optionsData;
    }

    public function processMetadata(array $metadata, string $countryId, string $postalCode, int $scopeId = null): array
    {
        return $metadata;
    }

    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        return $compatibilityData;
    }
}
