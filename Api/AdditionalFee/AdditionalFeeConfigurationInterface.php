<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\AdditionalFee;

use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;

/**
 * @api
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
     * @return Phrase
     */
    public function getLabel(): Phrase;
}
