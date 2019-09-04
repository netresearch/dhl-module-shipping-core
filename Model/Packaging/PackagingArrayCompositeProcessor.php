<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Model\Packaging\ArrayProcessor\ShippingOptionsProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackagingArrayCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackagingArrayCompositeProcessor
{
    /**
     * @var ShippingOptionsProcessorInterface[]
     */
    private $shippingOptionsProcessors;

    /**
     * PackagingArrayCompositeProcessor constructor.
     *
     * @param ShippingOptionsProcessorInterface[] $shippingOptionsProcessors
     */
    public function __construct(array $shippingOptionsProcessors = [])
    {
        $this->shippingOptionsProcessors = $shippingOptionsProcessors;
    }

    /**
     * Receive an array of shipping option data and modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     *
     * @return mixed[]
     */
    public function process(array $shippingData, Shipment $shipment): array
    {
        foreach ($this->shippingOptionsProcessors as $processor) {
            $shippingData = $processor->process($shippingData, $shipment);
        }

        return $shippingData;
    }
}
