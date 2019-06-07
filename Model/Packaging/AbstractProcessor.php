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
use Dhl\ShippingCore\Api\ShippingOptions\PackagingProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class AbstractProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class AbstractProcessor implements PackagingProcessorInterface
{
    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionsGroupName
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionsGroupName): array
    {
        return $optionsData;
    }

    /**
     * @param MetadataInterface $metadata
     * @param Shipment $shipment
     * @return MetadataInterface
     */
    public function processMetadata(MetadataInterface $metadata, Shipment $shipment): MetadataInterface
    {
        return $metadata;
    }

    /**
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(
        array $itemData,
        Shipment $shipment
    ): array {
        foreach ($itemData as $item) {
            $item->setShippingOptions(
                $this->processShippingOptions(
                    $item->getShippingOptions(),
                    $shipment,
                    PackagingDataProvider::GROUP_ITEM
                )
            );
        }

        return $itemData;
    }

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param Shipment $shipment
     * @return CompatibilityInterface[]
     */
    public function processCompatibilityData(array $compatibilityData, Shipment $shipment): array
    {
        return $compatibilityData;
    }
}
