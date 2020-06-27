<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;

/**
 * @api
 */
interface GlobalProcessorInterface
{
    /**
     * @param CarrierDataInterface $carrierData
     * @return CarrierDataInterface
     *
     * @throws \InvalidArgumentException
     */
    public function process(CarrierDataInterface $carrierData): CarrierDataInterface;
}
