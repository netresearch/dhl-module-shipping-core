<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class MetadataProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface MetadataProcessorInterface
{
    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     * @param Shipment $shipment
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata, Shipment $shipment): MetadataInterface;
}
