<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Dhl\ShippingCore\Api\Pipeline\RequestTracksStageInterface;

/**
 * Class RequestTracksPipelineProcessor
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class RequestTracksPipelineProcessor
{
    /**
     * Execute stages, pass previous result to next stage.
     *
     * @param TrackRequestInterface[] $requests
     * @param RequestTracksStageInterface[] $stages
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return TrackRequestInterface[]
     */
    public function process(array $requests, array $stages, ArtifactsContainerInterface $artifactsContainer): array
    {
        foreach ($stages as $stage) {
            $requests = $stage->execute($requests, $artifactsContainer);
        }

        return $requests;
    }
}
