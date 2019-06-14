<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Magento\Sales\Model\Order\Shipment;

/**
 * Interface PackagingArrayProcessorInterface
 *
 * @internal
 * @package Dhl\ShippingCore\Api\ShippingOptions
 * @author    Max Melzer <max.melzer@netresearch.de>
 */
interface PackagingArrayProcessorInterface
{
    /**
     * Receive an array of shipping option data and
     * modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     * @return mixed[]
     */
    public function processShippingOptions(
        array $shippingData,
        Shipment $shipment
    ): array;
}
