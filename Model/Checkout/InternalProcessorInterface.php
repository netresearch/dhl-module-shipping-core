<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

/**
 * Interface InternalProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 */
interface InternalProcessorInterface
{
    /**
     * Receive $checkoutData, modify it according to business logic and return the modified array.
     *
     * @param mixed[] $checkoutData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return mixed[]
     */
    public function process(array $checkoutData, string $countryId, string $postalCode, int $scopeId = null): array;
}
