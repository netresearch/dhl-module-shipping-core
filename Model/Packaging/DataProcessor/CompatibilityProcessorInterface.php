<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class CompatibilityProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface CompatibilityProcessorInterface
{
    /**
     * Receive an array of compatibility rule data items and
     * modify them according to business logic.
     *
     * @param CompatibilityInterface[] $compatibilityData
     * @param Shipment $shipment
     *
     * @return CompatibilityInterface[]
     */
    public function process(array $compatibilityData, Shipment $shipment): array;
}
