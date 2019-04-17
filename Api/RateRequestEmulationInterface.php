<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class RateRequestService
 *
 * @package Dhl\ShippingCore\Model\Emulation
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
     * @return bool|\Magento\Framework\DataObject|null
     */
    public function emulateRateRequest(string $carrierCode, RateRequest $request);
}
