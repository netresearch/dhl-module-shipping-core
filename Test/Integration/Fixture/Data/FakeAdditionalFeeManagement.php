<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Model\AdditionalFee\AdditionalFeeManagement;

/**
 * Class FakeAdditionalFeeManagement
 *
 * @package Dhl\ShippingCore\Test\Integration\Fixture\Data
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class FakeAdditionalFeeManagement extends AdditionalFeeManagement
{
    public function __construct($additionalFeeConfiguration = [])
    {
        $additionalFeeConfiguration[] = new FakeAdditionalFeeConfiguration();
        parent::__construct($additionalFeeConfiguration);
    }
}
