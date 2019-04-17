<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Quote\Model\Quote;

/**
 * Interface CodSupportInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Paul Siedler <paul.siedler@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface CodSupportInterface
{
    /**
     * Determines if a carrier has support for Cash on Delivery payment methods.
     *
     * @param Quote $quote
     * @return bool
     */
    public function hasCodSupport(Quote $quote): bool;
}
