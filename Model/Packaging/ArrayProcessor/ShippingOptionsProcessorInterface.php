<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\ArrayProcessor;

use Magento\Sales\Model\Order\Shipment;

/**
 * Class ShippingOptionsProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Packaging\ArrayProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface ShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option data and modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, Shipment $shipment): array;
}
