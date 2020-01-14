<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Dhl\ShippingCore\Api\Pipeline\RequestTracksPipelineInterface;
use Dhl\ShippingCore\Api\Pipeline\RequestTracksStageInterface;

/**
 * Class DeleteShipmentsPipeline
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class RequestTracksPipeline implements RequestTracksPipelineInterface
{
    /**
     * @var RequestTracksPipelineProcessor
     */
    private $pipelineProcessor;

    /**
     * @var ArtifactsContainerInterfaceFactory
     */
    private $artifactsContainerFactory;

    /**
     * @var RequestTracksStageInterface[]
     */
    private $stages;

    /**
     * DeleteShipmentsPipeline constructor.
     *
     * @param RequestTracksPipelineProcessor $pipelineProcessor
     * @param ArtifactsContainerInterfaceFactory $artifactsContainerFactory
     * @param RequestTracksStageInterface[] $stages
     */
    public function __construct(
        RequestTracksPipelineProcessor $pipelineProcessor,
        ArtifactsContainerInterfaceFactory $artifactsContainerFactory,
        array $stages = []
    ) {
        $this->pipelineProcessor = $pipelineProcessor;
        $this->artifactsContainerFactory = $artifactsContainerFactory;
        $this->stages = $stages;
    }

    /**
     * Initialize pipeline and execute configured stages.
     *
     * @param int $storeId
     * @param TrackRequestInterface[] $requests
     * @return ArtifactsContainerInterface
     */
    public function run(int $storeId, array $requests): ArtifactsContainerInterface
    {
        $artifactsContainer = $this->artifactsContainerFactory->create();
        $artifactsContainer->setStoreId($storeId);

        $this->pipelineProcessor->process($requests, $this->stages, $artifactsContainer);

        return $artifactsContainer;
    }
}
