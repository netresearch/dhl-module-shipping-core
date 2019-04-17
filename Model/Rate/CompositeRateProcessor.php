<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rate;

use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class CompositeRateProcessor
 *
 * @package     Dhl\ShippingCore\Model\Rate
 * @author      Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link        https://www.netresearch.de/
 */
class CompositeRateProcessor implements RateProcessorInterface
{
    /**
     * @var string
     */
    private $carrierCode;

    /**
     * @var RateProcessorInterface[]
     */
    private $processors = [];

    /**
     * CompositeRateProcessor constructor.
     * @param string $carrierCode
     * @param RateProcessorInterface[] $processors
     */
    public function __construct($carrierCode, array $processors)
    {
        $this->carrierCode = $carrierCode;
        $this->processors = $processors;
    }

    /**
     * @param array $methods
     * @param RateRequest|null $request
     * @return array
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        foreach ($this->processors as $processor) {
            $methods = $processor->processMethods($methods, $request);
        }

        return $methods;
    }
}
