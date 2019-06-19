<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Processor;

use Dhl\ShippingCore\Api\Data\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\ShipmentResponseProcessorInterface;

/**
 * Class ShipmentResponseProcessor
 *
 * @package Dhl\ShippingCore\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
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
