<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate\ResponseProcessor;

use Dhl\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * Class FreeShipping
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class FreeShipping implements RateResponseProcessorInterface
{
    /**
     * If cart price rules for free shipping applied, set shipping price to "0".
     *
     * @param Method[] $methods List of rate methods
     * @param RateRequest|null $request The rate request
     *
     * @return Method[]
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        foreach ($methods as $method) {
            // Check if cart price rule was applied
            if ($request->getFreeShipping()) {
                $method->setPrice(0.0);
            }
        }

        return $methods;
    }
}
