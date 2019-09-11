<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Quote\Model\Quote;

/**
 * Interface AdditionalFeeConfigurationInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface AdditionalFeeConfigurationInterface
{
    /**
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * @param Quote $quote
     * @return bool
     */
    public function isActive(Quote $quote): bool;

    /**
     * @param Quote $quote
     * @return float
     */
    public function getServiceCharge(Quote $quote): float;

    /**
     * @return string
     */
    public function getLabel(): string;
}
