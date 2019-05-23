<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\ShippingOptions\CheckoutProcessorInterface;
use Dhl\ShippingCore\Api\ShippingOptions\PackagingProcessorInterface;
use Magento\Sales\Model\Order;

/**
 * Class PackagingDataCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackagingDataCompositeProcessor implements PackagingProcessorInterface
{
    /**
     * @var PackagingProcessorInterface[]
     */
    private $processors;

    /**
     * @var CheckoutProcessorInterface[]
     */
    private $checkoutProcessors;

    /**
     * PackagingDataCompositeProcessor constructor.
     *
     * @param PackagingProcessorInterface[] $processors
     * @param CheckoutProcessorInterface[] $checkoutProcessors
     */
    public function __construct(array $processors = [], array $checkoutProcessors = [])
    {
        $this->processors = $processors;
        $this->checkoutProcessors = $checkoutProcessors;
    }

    public function processShippingOptions(
        array $optionsData,
        Order $order
    ): array {
        $result = $optionsData;

        if ($order->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processShippingOptions(
                    $result,
                    $order->getShippingAddress()->getCountryId(),
                    $order->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processShippingOptions(
                $result,
                $order
            );
        }

        return $result;
    }

    public function processMetadata(
        array $metadata,
        Order $order
    ): array {
        $result = $metadata;

        if ($order->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processMetadata(
                    $result,
                    $order->getShippingAddress()->getCountryId(),
                    $order->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processMetadata(
                $result,
                $order
            );
        }

        return $result;
    }

    public function processCompatibilityData(
        array $compatibilityData,
        Order $order
    ): array {
        $result = $compatibilityData;

        if ($order->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processCompatibilityData(
                    $result,
                    $order->getShippingAddress()->getCountryId(),
                    $order->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processCompatibilityData(
                $result,
                $order
            );
        }

        return $result;
    }
}
