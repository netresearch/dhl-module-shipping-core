<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\ShippingOptions\PackagingProcessorInterface;
use Magento\Sales\Api\Data\OrderInterface;

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
     * PackagingDataCompositeProcessor constructor.
     *
     * @param PackagingProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    public function processShippingOptions(
        array $optionsData,
        OrderInterface $order,
        int $scopeId = null
    ): array {
        $result = $optionsData;
        foreach ($this->processors as $processor) {
            $result = $processor->processShippingOptions(
                $result,
                $order,
                $scopeId
            );
        }

        return $result;
    }

    public function processMetadata(
        array $metadata,
        OrderInterface $order,
        int $scopeId = null
    ): array {
        $result = $metadata;
        foreach ($this->processors as $processor) {
            $result = $processor->processMetadata(
                $result,
                $order,
                $scopeId
            );
        }

        return $result;
    }

    public function processCompatibilityData(
        array $compatibilityData,
        OrderInterface $order,
        int $scopeId = null
    ): array {
        $result = $compatibilityData;
        foreach ($this->processors as $processor) {
            $result = $processor->processCompatibilityData(
                $result,
                $order,
                $scopeId
            );
        }

        return $result;
    }
}
