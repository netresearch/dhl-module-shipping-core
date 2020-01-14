<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\TrackResponseProcessorInterface;

/**
 * Class TrackResponseProcessor
 *
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
     * Perform actions after receiving the "request tracks" response.
     *
     * @param TrackResponseInterface[] $trackResponses
     * @param TrackErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $trackResponses, array $errorResponses)
    {
        foreach ($this->processors as $processor) {
            $processor->processResponse($trackResponses, $errorResponses);
        }
    }
}
