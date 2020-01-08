<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Rate;

use Dhl\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * Class RateResponseProcessor
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class RateResponseProcessor implements RateResponseProcessorInterface
{
    /**
     * @var RateResponseProcessorInterface[]
     */
    private $processors;

    /**
     * RateResponseProcessor constructor.
     *
     * @param RateResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Perform read/write actions on the webservice rate result.
     *
     * @param Method[] $methods
     * @param RateRequest|null $request
     * @return Method[]
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        foreach ($this->processors as $processor) {
            $methods = $processor->processMethods($methods, $request);
        }

        return $methods;
    }
}
