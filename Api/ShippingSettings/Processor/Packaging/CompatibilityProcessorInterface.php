<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class CompatibilityProcessorInterface
 *
 * @api
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface CompatibilityProcessorInterface
{
    /**
     * Receive an array of compatibility rule data items and
     * modify them according to business logic.
     *
     * @param CompatibilityInterface[] $compatibilityData
     * @param ShipmentInterface $shipment
     *
     * @return CompatibilityInterface[]
     */
    public function process(array $compatibilityData, ShipmentInterface $shipment): array;
}
