<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Processor;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\TrackResponseProcessorInterface;

/**
 * Class TrackResponseProcessor
 *
 * @package Dhl\ShippingCore\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class TrackResponseProcessor implements TrackResponseProcessorInterface
{
    /**
     * @var TrackResponseProcessorInterface[]
     */
    private $processors;

    /**
     * @param TrackResponseProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * Perform actions after receiving the track response.
     *
     * @param TrackResponseInterface[] $trackResponses Shipment cancellation responses
     * @param TrackErrorResponseInterface[] $errorResponses Shipment cancellation errors
     */
    public function processResponse(array $trackResponses, array $errorResponses)
    {
        foreach ($this->processors as $processor) {
            $processor->processResponse($trackResponses, $errorResponses);
        }
    }
}
