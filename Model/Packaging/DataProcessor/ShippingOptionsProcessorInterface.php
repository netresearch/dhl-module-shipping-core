<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ShippingOptionsProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface ShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option items and modify them according to business logic.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, Shipment $shipment): array;
}
