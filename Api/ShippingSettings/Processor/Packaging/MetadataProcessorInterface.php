<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class MetadataProcessorInterface
 *
 * @api
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface MetadataProcessorInterface
{
    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     * @param ShipmentInterface $shipment
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata, ShipmentInterface $shipment): MetadataInterface;
}
