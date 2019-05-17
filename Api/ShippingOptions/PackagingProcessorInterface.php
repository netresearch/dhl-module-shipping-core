<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\ShippingOptions;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface PackagingProcessorInterface
 *
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface PackagingProcessorInterface
{
    /**
     * Get the carrier code the processor can handle
     *
     * @return string
     */
    public function getCarrier(): string;

    /**
     * Receive $carrierData, modify it according to business logic and return the modified array.
     *
     * @param mixed[] $carrierData
     * @param OrderInterface $order
     * @return mixed[]
     */
    public function process(array $carrierData, OrderInterface $order): array;
}
