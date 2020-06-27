<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;

/**
 * Retrieve tracking information from a web service by passing a track request through multiple configurable stages.
 *
 * When the tracking history (or any information related to tracking numbers) is to be fetched from a web service,
 * a "track request" object needs to be created. Pipeline stages process and transform the request to become suitable
 * for sending it to the carrier-specific web service, then send and transform it back to application data.
 * The result of running the pipeline is the artifacts container where all stages add their output data to.
 * Consequently, the overall pipeline result (e.g. the tracking events) can afterwards be obtained from the artifacts
 * container.
 *
 * @see ArtifactsContainerInterface
 * @see RequestTracksStageInterface
 * @see TrackResponseProcessorInterface
 *
 * @api
 */
interface RequestTracksPipelineInterface
{
    /**
     * Initialize pipeline and execute configured stages.
     *
     * @param int $storeId
     * @param TrackRequestInterface[] $requests
     * @return ArtifactsContainerInterface
     */
    public function run(int $storeId, array $requests): ArtifactsContainerInterface;
}
