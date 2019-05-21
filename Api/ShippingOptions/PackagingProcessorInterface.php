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
     * Receive an array of shipping option items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param mixed[] $optionsData
     * @param OrderInterface $order
     * @param int|null $scopeId
     * @return mixed[]
     */
    public function processShippingOptions(
        array $optionsData,
        OrderInterface $order,
        int $scopeId = null
    ): array;

    /**
     * Receive shipping option metadata, modify it according to business logic and return the modified array.
     *
     * @param array $metadata
     * @param OrderInterface $order
     * @param int|null $scopeId
     * @return array
     */
    public function processMetadata(
        array $metadata,
        OrderInterface $order,
        int $scopeId = null
    ): array;

    /**
     * Receive an array of compatibility rule data items,
     * modify them according to business logic
     * and return the modified array.
     *
     * @param array $compatibilityData
     * @param OrderInterface $order
     * @param int|null $scopeId
     * @return array
     */
    public function processCompatibilityData(
        array $compatibilityData,
        OrderInterface $order,
        int $scopeId = null
    ): array;
}