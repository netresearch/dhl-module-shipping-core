<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

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
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionsGroupName): array
    {
        return $optionsData;
    }

    public function processMetadata(array $metadata, Shipment $shipment): array
    {
        return $metadata;
    }

    public function processCompatibilityData(array $compatibilityData, Shipment $shipment): array
    {
        return $compatibilityData;
    }
}
