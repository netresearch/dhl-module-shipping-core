<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\CompatibilityProcessorInterface as CheckoutCompatibilityProcessorInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\MetadataProcessorInterface as CheckoutMetadataProcessorInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\ShippingOptionsProcessorInterface as CheckoutShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\CompatibilityProcessorInterface as PackagingCompatibilityProcessorInterface;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\ShippingOptionsProcessorInterface as PackagingShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\MetadataProcessorInterface as PackagingMetadataProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackagingDataCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackagingDataCompositeProcessor
{
    /**
     * @var CheckoutShippingOptionsProcessorInterface[]
     */
    private $checkoutServiceOptionsProcessors;

    /**
     * @var CheckoutMetadataProcessorInterface[]
     */
    private $checkoutMetadataProcessors;

    /**
     * @var CheckoutCompatibilityProcessorInterface[]
     */
    private $checkoutCompatibilityProcessors;

    /**
     * @var PackagingShippingOptionsProcessorInterface[]
     */
    private $packagingServiceOptionsProcessors;

    /**
     * @var PackagingShippingOptionsProcessorInterface[]
     */
    private $packagingPackageOptionsProcessors;

    /**
     * @var PackagingShippingOptionsProcessorInterface[]
     */
    private $packagingItemOptionsProcessors;

    /**
     * @var PackagingMetadataProcessorInterface[]
     */
    private $packagingMetadataProcessors;

    /**
     * @var PackagingCompatibilityProcessorInterface[]
     */
    private $packagingCompatibilityProcessors;

    /**
     * CheckoutDataCompositeProcessor constructor.
     *
     * @param CheckoutShippingOptionsProcessorInterface[] $checkoutServiceOptionsProcessors
     * @param CheckoutMetadataProcessorInterface[] $checkoutMetadataProcessors
     * @param CheckoutCompatibilityProcessorInterface[] $checkoutCompatibilityProcessors
     * @param PackagingShippingOptionsProcessorInterface[] $packagingServiceOptionsProcessors
     * @param PackagingShippingOptionsProcessorInterface[] $packagingPackageOptionsProcessors
     * @param PackagingShippingOptionsProcessorInterface[] $packagingItemOptionsProcessors
     * @param PackagingMetadataProcessorInterface[] $packagingMetadataProcessors
     * @param PackagingCompatibilityProcessorInterface[] $packagingCompatibilityProcessors
     */
    public function __construct(
        array $checkoutServiceOptionsProcessors = [],
        array $checkoutMetadataProcessors = [],
        array $checkoutCompatibilityProcessors = [],
        array $packagingServiceOptionsProcessors = [],
        array $packagingPackageOptionsProcessors = [],
        array $packagingItemOptionsProcessors = [],
        array $packagingMetadataProcessors = [],
        array $packagingCompatibilityProcessors = []
    ) {
        $this->checkoutServiceOptionsProcessors = $checkoutServiceOptionsProcessors;
        $this->checkoutMetadataProcessors = $checkoutMetadataProcessors;
        $this->checkoutCompatibilityProcessors = $checkoutCompatibilityProcessors;
        $this->packagingServiceOptionsProcessors = $packagingServiceOptionsProcessors;
        $this->packagingPackageOptionsProcessors = $packagingPackageOptionsProcessors;
        $this->packagingItemOptionsProcessors = $packagingItemOptionsProcessors;
        $this->packagingMetadataProcessors = $packagingMetadataProcessors;
        $this->packagingCompatibilityProcessors = $packagingCompatibilityProcessors;
    }

    /**
     * @param ShippingDataInterface $packagingData
     * @param Shipment $shipment
     *
     * @return ShippingDataInterface
     */
    public function process(ShippingDataInterface $packagingData, Shipment $shipment): ShippingDataInterface
    {
        foreach ($packagingData->getCarriers() as $carrierData) {
            $carrierData->setPackageOptions(
                $this->processPackageOptions(
                    $carrierData->getPackageOptions(),
                    $shipment
                )
            );

            $carrierData->setServiceOptions(
                $this->processServiceOptions(
                    $carrierData->getServiceOptions(),
                    $shipment
                )
            );

            $carrierData->setItemOptions(
                $this->processItemOptions(
                    $carrierData->getItemOptions(),
                    $shipment
                )
            );

            $carrierData->setCompatibilityData(
                $this->processCompatibilityData(
                    $carrierData->getCompatibilityData(),
                    $shipment
                )
            );

            // metadata is optional
            if ($carrierData->getMetadata()) {
                $carrierData->setMetadata(
                    $this->processMetadata(
                        $carrierData->getMetadata(),
                        $shipment
                    )
                );
            }
        }

        return $packagingData;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     *
     * @return ShippingOptionInterface[]
     */
    private function processPackageOptions(
        array $optionsData,
        Shipment $shipment
    ): array {
        /** @var PackagingShippingOptionsProcessorInterface $processor */
        foreach ($this->packagingPackageOptionsProcessors as $processor) {
            $optionsData = $processor->process(
                $optionsData,
                $shipment
            );
        }

        return $optionsData;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     *
     * @return ShippingOptionInterface[]
     */
    private function processServiceOptions(
        array $optionsData,
        Shipment $shipment
    ): array {
        if ($shipment->getShippingAddress()) {
            /** @var CheckoutShippingOptionsProcessorInterface $processor */
            foreach ($this->checkoutServiceOptionsProcessors as $processor) {
                $optionsData = $processor->process(
                    $optionsData,
                    $shipment->getShippingAddress()->getCountryId(),
                    $shipment->getShippingAddress()->getPostcode(),
                    (int) $shipment->getStoreId()
                );
            }
        }

        /** @var PackagingShippingOptionsProcessorInterface $processor */
        foreach ($this->packagingServiceOptionsProcessors as $processor) {
            $optionsData = $processor->process(
                $optionsData,
                $shipment
            );
        }

        return $optionsData;
    }

    /**
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    private function processItemOptions(array $itemData, Shipment $shipment): array
    {
        /** @var PackagingShippingOptionsProcessorInterface $processor */
        foreach ($this->packagingItemOptionsProcessors as $processor) {
            $itemData = $processor->process($itemData, $shipment);
        }

        // Apply checkout processors to item based shipping options as well
        if ($shipment->getShippingAddress()) {
            /** @var CheckoutShippingOptionsProcessorInterface $processor */
            foreach ($this->checkoutServiceOptionsProcessors as $processor) {
                foreach ($itemData as $item) {
                    $item->setShippingOptions(
                        $processor->process(
                            $item->getShippingOptions(),
                            $shipment->getShippingAddress()->getCountryId(),
                            $shipment->getShippingAddress()->getPostcode(),
                            (int) $shipment->getStoreId()
                        )
                    );
                }
            }
        }

        return $itemData;
    }

    /**
     * @param MetadataInterface $metadata
     * @param Shipment $shipment
     *
     * @return MetadataInterface
     */
    private function processMetadata(
        MetadataInterface $metadata,
        Shipment $shipment
    ): MetadataInterface {
        if ($shipment->getShippingAddress()) {
            /** @var CheckoutMetadataProcessorInterface $processor */
            foreach ($this->checkoutMetadataProcessors as $processor) {
                $metadata = $processor->process($metadata);
            }
        }

        /** @var PackagingMetadataProcessorInterface $processor */
        foreach ($this->packagingMetadataProcessors as $processor) {
            $metadata = $processor->process(
                $metadata,
                $shipment
            );
        }

        return $metadata;
    }

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param Shipment $shipment
     *
     * @return CompatibilityInterface[]
     */
    private function processCompatibilityData(
        array $compatibilityData,
        Shipment $shipment
    ): array {
        if ($shipment->getShippingAddress()) {
            /** @var CheckoutCompatibilityProcessorInterface $processor */
            foreach ($this->checkoutCompatibilityProcessors as $processor) {
                $compatibilityData = $processor->process($compatibilityData);
            }
        }

        /** @var PackagingCompatibilityProcessorInterface $processor */
        foreach ($this->packagingCompatibilityProcessors as $processor) {
            $compatibilityData = $processor->process(
                $compatibilityData,
                $shipment
            );
        }

        return $compatibilityData;
    }
}
