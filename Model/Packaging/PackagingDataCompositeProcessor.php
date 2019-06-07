<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
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

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     *
     * @return ShippingOptionInterface[]
     */
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

    /**
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(array $itemData, Shipment $shipment): array
    {
        foreach ($this->processors as $processor) {
            $itemData = $processor->processItemOptions(
                $itemData,
                $shipment
            );
        }

        return $itemData;
    }

    /**
     * @param MetadataInterface $metadata
     * @param Shipment $shipment
     *
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        Shipment $shipment
    ): MetadataInterface {
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

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param Shipment $shipment
     *
     * @return CompatibilityInterface[]
     */
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
