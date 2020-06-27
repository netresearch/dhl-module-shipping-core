<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;

/**
 * Post-process tracks and errors as retrieved from the request tracks pipeline.
 *
 * Response processors offer a dedicated way to perform additional actions on the artifacts collected during pipeline
 * execution. The default implementation is a composite processor. Any actual processors which implement this interface
 * may be created and added via configuration.
 *
 * @see RequestTracksPipelineInterface
 *
 * @api
 */
interface TrackResponseProcessorInterface
{
    /**
     * Perform actions after receiving the "request tracks" response.
     *
     * @param TrackResponseInterface[] $trackResponses
     * @param TrackErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $trackResponses, array $errorResponses);
}
