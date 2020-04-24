<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Unit;

use Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Compatibility\PreProcessor;

class PreProcessorMock extends PreProcessor
{
    public function __construct()
    {}

    /**
     * Does nothing but return the input
     *
     * @param CarrierDataInterface $carrierData
     * @return CarrierDataInterface
     */
    public function process(CarrierDataInterface $carrierData): CarrierDataInterface
    {
        return $carrierData;
    }
}
