<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\ShippingOptions;

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
     * Receive an array of shipping option items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param mixed[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     * @return mixed[]
     */
    public function processShippingOptions(
        array $optionsData,
        Shipment $shipment,
        string $optionGroupName
    ): array;

    /**
     * Receive shipping option metadata, modify it according to business logic and return the modified array.
     *
     * @param array $metadata
     * @param Shipment $shipment
     * @return array
     */
    public function processMetadata(
        array $metadata,
        Shipment $shipment
    ): array;

    /**
     * Receive an array of compatibility rule data items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param array $compatibilityData
     * @param Shipment $shipment
     * @return array
     */
    public function processCompatibilityData(
        array $compatibilityData,
        Shipment $shipment
    ): array;
}
