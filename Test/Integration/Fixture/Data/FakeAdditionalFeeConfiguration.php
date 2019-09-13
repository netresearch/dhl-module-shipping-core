<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Api\AdditionalFeeConfigurationInterface;
use Magento\Quote\Model\Quote;

/**
 * Class FakeAdditionalFeeConfiguration
 *
 * @package Dhl\ShippingCore\Test\Integration\Fixture\Data
 * @author Max Melzer <max.melzer@netresearch.de>
 */
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

    public function getLabel(): string
    {
        return self::LABEL;
    }
}
