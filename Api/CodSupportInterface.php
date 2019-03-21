<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Interface CodSupportInterface
 *
 * @package Dhl\ShippingCore\Api
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link http://www.netresearch.de/
 */
interface CodSupportInterface
{
    /**
     * Determines if a Carrier has support for Cash on Delivery payment methods
     *
     * @param CartInterface|Quote $quote
     * @return bool
     */
    public function hasCodSupport(CartInterface $quote): bool;
}
