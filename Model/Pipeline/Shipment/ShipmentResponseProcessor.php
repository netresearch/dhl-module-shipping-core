<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment;

use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;

class ShipmentResponseProcessor implements ShipmentResponseProcessorInterface
{
    /**
     * @var ShipmentResponseProcessorInterface[]
     */
    private $processors;

    /**
     * @param ShipmentResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Perform actions after receiving the shipment response.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses)
    {
        foreach ($this->processors as $processor) {
            $processor->processResponse($labelResponses, $errorResponses);
        }
    }
}
