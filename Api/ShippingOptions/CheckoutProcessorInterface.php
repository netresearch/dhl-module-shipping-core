<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\ShippingOptions;

use Dhl\ShippingCore\Model\Checkout\InternalProcessorInterface;

/**
 * Interface CheckoutProcessorInterface
 *
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface CheckoutProcessorInterface
{
    /**
     * Get the carrier code the processor can handle
     *
     * @return string
     */
    public function getCarrier(): string;

    /**
     * Receive the $carrierData, modify it according to business logic and return the modified array.
     *
     * @param mixed[] $carrierData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return mixed[]
     */
    public function process(array $carrierData, string $countryId, string $postalCode, int $scopeId = null): array;
}
