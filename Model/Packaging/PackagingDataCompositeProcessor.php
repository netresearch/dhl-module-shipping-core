<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\ShippingOptions\CheckoutProcessorInterface;
use Dhl\ShippingCore\Api\ShippingOptions\PackagingProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

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
        Shipment $shipment,
        string $optionGroupName
    ): array {
        $result = $optionsData;

        if ($shipment->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processShippingOptions(
                    $result,
                    $shipment->getShippingAddress()->getCountryId(),
                    $shipment->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processShippingOptions(
                $result,
                $shipment,
                $optionGroupName
            );
        }

        return $result;
    }

    public function processMetadata(
        array $metadata,
        Shipment $shipment
    ): array {
        $result = $metadata;

        if ($shipment->getOrder()->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processMetadata(
                    $result,
                    $shipment->getShippingAddress()->getCountryId(),
                    $shipment->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processMetadata(
                $result,
                $shipment
            );
        }

        return $result;
    }

    public function processCompatibilityData(
        array $compatibilityData,
        Shipment $shipment
    ): array {
        $result = $compatibilityData;

        if ($shipment->getShippingAddress()) {
            foreach ($this->checkoutProcessors as $processor) {
                $result = $processor->processCompatibilityData(
                    $result,
                    $shipment->getShippingAddress()->getCountryId(),
                    $shipment->getShippingAddress()->getPostcode()
                );
            }
        }

        foreach ($this->processors as $processor) {
            $result = $processor->processCompatibilityData(
                $result,
                $shipment
            );
        }

        return $result;
    }
}
