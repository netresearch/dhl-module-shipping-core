<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

/**
 * Class CheckoutArrayCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class CheckoutArrayCompositeProcessor implements CheckoutArrayProcessorInterface
{
    /**
     * @var CheckoutArrayProcessorInterface[]
     */
    private $arrayProcessors;

    /**
     * CheckoutArrayCompositeProcessor constructor.
     *
     * @param CheckoutArrayProcessorInterface[] $arrayProcessors
     */
    public function __construct(array $arrayProcessors = [])
    {
        $this->arrayProcessors = $arrayProcessors;
    }

    /**
     * @param mixed[] $shippingData
     * @param int     $storeId
     *
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, int $storeId): array
    {
        foreach ($this->arrayProcessors as $processor) {
            $shippingData = $processor->processShippingOptions($shippingData, $storeId);
        }

        return $shippingData;
    }
}
