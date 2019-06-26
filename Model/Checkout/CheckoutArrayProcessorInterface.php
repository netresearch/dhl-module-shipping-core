<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

/**
 * Interface CheckoutArrayProcessorInterface
 *
 * @internal
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface CheckoutArrayProcessorInterface
{
    /**
     * Receive an array of shipping option data and
     * modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param int     $storeId
     *
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, int $storeId): array;
}
