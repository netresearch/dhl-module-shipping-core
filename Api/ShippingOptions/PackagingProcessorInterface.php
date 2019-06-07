<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingOptions;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Interface PackagingProcessorInterface
 *
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface PackagingProcessorInterface
{
    /**
     * Receive an array of shipping option items and
     * modify them according to business logic.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $optionsData,
        Shipment $shipment,
        string $optionGroupName
    ): array;

    /**
     * Recieve an array of shipment items with corresponding shipping options
     * and modify them according to business logic.
     *
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(
        array $itemData,
        Shipment $shipment
    ): array;

    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     * @param Shipment $shipment
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        Shipment $shipment
    ): MetadataInterface;

    /**
     * Receive an array of compatibility rule data items and
     * modify them according to business logic.
     *
     * @param CompatibilityInterface[] $compatibilityData
     * @param Shipment $shipment
     * @return CompatibilityInterface[]
     */
    public function processCompatibilityData(
        array $compatibilityData,
        Shipment $shipment
    ): array;
}
