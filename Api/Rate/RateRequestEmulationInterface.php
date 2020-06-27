<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

/**
 * @api
 */
interface RateRequestEmulationInterface
{
    /**
     * Emulates rate request for the given carrier
     *
     * @param string $carrierCode Carrier code to emulate
     * @param RateRequest $request Original rate request
     * @return Result|bool
     */
    public function emulateRateRequest(string $carrierCode, RateRequest $request);
}
