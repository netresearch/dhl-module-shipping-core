<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Rate\Result;

/**
 * Class RateRequestEmulationInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Paul Siedler <paul.siedler@netresearch.de>
 * @link    https://www.netresearch.de/
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
