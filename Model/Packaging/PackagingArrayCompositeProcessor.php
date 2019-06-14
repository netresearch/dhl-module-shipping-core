<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackagingArrayCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackagingArrayCompositeProcessor implements PackagingArrayProcessorInterface
{
    /**
     * @var PackagingArrayProcessorInterface[]
     */
    private $arrayProcessors;

    /**
     * PackagingArrayCompositeProcessor constructor.
     *
     * @param PackagingArrayProcessorInterface[] $arrayProcessors
     */
    public function __construct(array $arrayProcessors = [])
    {
        $this->arrayProcessors = $arrayProcessors;
    }

    /**
     * @param mixed[] $shippingData
     * @param Shipment $shipment
     * @return mixed[]
     */
    public function processShippingOptions(array $shippingData, Shipment $shipment): array
    {
        foreach ($this->arrayProcessors as $processor) {
            $shippingData = $processor->processShippingOptions($shippingData, $shipment);
        }

        return $shippingData;
    }
}
