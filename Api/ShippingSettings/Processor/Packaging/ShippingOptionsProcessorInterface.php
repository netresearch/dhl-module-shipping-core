<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * @api
 */
interface ShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option items and modify them according to business logic.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array;
}
