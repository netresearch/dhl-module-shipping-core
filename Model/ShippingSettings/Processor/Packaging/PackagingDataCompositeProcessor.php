<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\CompatibilityProcessorInterface
    as CheckoutCompatibilityProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\GlobalProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\MetadataProcessorInterface
    as CheckoutMetadataProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\ShippingOptionsProcessorInterface
    as CheckoutShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\CompatibilityProcessorInterface
    as PackagingCompatibilityProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ItemShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\MetadataProcessorInterface
    as PackagingMetadataProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface
    as PackagingShippingOptionsProcessorInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
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
     * @var ItemShippingOptionsProcessorInterface[]
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
     * @var GlobalProcessorInterface[]
     */
    private $globalProcessors;

    public function __construct(
        array $checkoutServiceOptionsProcessors = [],
        array $checkoutMetadataProcessors = [],
        array $checkoutCompatibilityProcessors = [],
        array $packagingServiceOptionsProcessors = [],
        array $packagingPackageOptionsProcessors = [],
        array $packagingItemOptionsProcessors = [],
        array $packagingMetadataProcessors = [],
        array $packagingCompatibilityProcessors = [],
        array $globalProcessors = []
    ) {
        $this->checkoutServiceOptionsProcessors = $checkoutServiceOptionsProcessors;
        $this->checkoutMetadataProcessors = $checkoutMetadataProcessors;
        $this->checkoutCompatibilityProcessors = $checkoutCompatibilityProcessors;
        $this->packagingServiceOptionsProcessors = $packagingServiceOptionsProcessors;
        $this->packagingPackageOptionsProcessors = $packagingPackageOptionsProcessors;
        $this->packagingItemOptionsProcessors = $packagingItemOptionsProcessors;
        $this->packagingMetadataProcessors = $packagingMetadataProcessors;
        $this->packagingCompatibilityProcessors = $packagingCompatibilityProcessors;
        $this->globalProcessors = $globalProcessors;
    }

    /**
     * @param ShippingDataInterface $packagingData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingDataInterface
     */
    public function process(ShippingDataInterface $packagingData, ShipmentInterface $shipment): ShippingDataInterface
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

            foreach ($this->globalProcessors as $processor) {
                $processor->process($carrierData);
            }
        }

        return $packagingData;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    private function processPackageOptions(
        array $optionsData,
        ShipmentInterface $shipment
    ): array {
        /** @var OrderAddressInterface $shippingAddress */
        $shippingAddress = $shipment->getShippingAddress();

        foreach ($this->checkoutServiceOptionsProcessors as $processor) {
            $optionsData = $processor->process(
                $optionsData,
                $shippingAddress->getCountryId(),
                $shippingAddress->getPostcode(),
                (int)$shipment->getStoreId()
            );
        }
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
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    private function processServiceOptions(
        array $optionsData,
        ShipmentInterface $shipment
    ): array {
        if ($shipment->getShippingAddress()) {
            foreach ($this->checkoutServiceOptionsProcessors as $processor) {
                $optionsData = $processor->process(
                    $optionsData,
                    $shipment->getShippingAddress()->getCountryId(),
                    $shipment->getShippingAddress()->getPostcode(),
                    (int)$shipment->getStoreId()
                );
            }
        }

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
     * @param ShipmentInterface $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    private function processItemOptions(array $itemData, ShipmentInterface $shipment): array
    {
        foreach ($this->packagingItemOptionsProcessors as $processor) {
            $itemData = $processor->process($itemData, $shipment);
        }

        /** @var OrderAddressInterface $shippingAddress */
        $shippingAddress = $shipment->getShippingAddress();

        // Apply checkout processors to item based shipping options as well
        if ($shippingAddress) {
            foreach ($this->checkoutServiceOptionsProcessors as $processor) {
                foreach ($itemData as $item) {
                    $item->setShippingOptions(
                        $processor->process(
                            $item->getShippingOptions(),
                            $shippingAddress->getCountryId(),
                            $shippingAddress->getPostcode(),
                            (int)$shipment->getStoreId()
                        )
                    );
                }
            }
        }

        return $itemData;
    }

    /**
     * @param MetadataInterface $metadata
     * @param ShipmentInterface $shipment
     *
     * @return MetadataInterface
     */
    private function processMetadata(
        MetadataInterface $metadata,
        ShipmentInterface $shipment
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
     * @param ShipmentInterface $shipment
     *
     * @return CompatibilityInterface[]
     */
    private function processCompatibilityData(
        array $compatibilityData,
        ShipmentInterface $shipment
    ): array {
        if ($shipment->getShippingAddress()) {
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
