<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Api\AdditionalFee\AdditionalFeeConfigurationInterface;
use Magento\Framework\Phrase;
use Magento\Quote\Model\Quote;

class FakeAdditionalFeeConfiguration implements AdditionalFeeConfigurationInterface
{
    const CARRIERCODE = 'testcarrier';

    const LABEL = 'testlabel';

    const CHARGE = 22.22;

    public function getCarrierCode(): string
    {
        return self::CARRIERCODE;
    }

    public function isActive(Quote $quote): bool
    {
        return true;
    }

    public function getServiceCharge(Quote $quote): float
    {
        return self::CHARGE;
    }

    public function getLabel(): Phrase
    {
        return __(self::LABEL);
    }
}
