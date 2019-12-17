<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\PaymentMethod;

use Magento\Quote\Model\Quote;

/**
 * Interface MethodAvailabilityInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Paul Siedler <paul.siedler@netresearch.de>
 * @link    https://www.netresearch.de/
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
