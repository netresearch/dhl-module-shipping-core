<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Model\Checkout\ArrayProcessor\ShippingOptionsProcessorInterface;

/**
 * Class CheckoutArrayCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class CheckoutArrayCompositeProcessor
{
    /**
     * @var ShippingOptionsProcessorInterface[]
     */
    private $shippingOptionsProcessors;

    /**
     * CheckoutArrayCompositeProcessor constructor.
     *
     * @param ShippingOptionsProcessorInterface[] $shippingOptionsProcessors
     */
    public function __construct(
        array $shippingOptionsProcessors = []
    ) {
        $this->shippingOptionsProcessors = $shippingOptionsProcessors;
    }

    /**
     * Receive an array of shipping option data and modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param int $storeId
     *
     * @return mixed[]
     */
    public function process(array $shippingData, int $storeId): array
    {
        foreach ($this->shippingOptionsProcessors as $processor) {
            $shippingData = $processor->process($shippingData, $storeId);
        }

        return $shippingData;
    }
}
