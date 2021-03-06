<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\PaymentMethod;

use Magento\Quote\Model\Quote;

/**
 * @api
 */
interface MethodAvailabilityInterface
{
    /**
     * Determines whether a payment method is available for the given quote.
     *
     * @param Quote $quote
     * @return bool
     */
    public function isAvailable(Quote $quote): bool;
}
