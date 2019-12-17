<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging;

use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class ShippingOptionsArrayProcessorInterface
 *
 * @api
 * @package Dhl\ShippingCore\Model\Packaging\ArrayProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface ShippingOptionsArrayProcessorInterface
{
    /**
     * Receive an array of shipping option data and modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param ShipmentInterface $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, ShipmentInterface $shipment): array;
}
