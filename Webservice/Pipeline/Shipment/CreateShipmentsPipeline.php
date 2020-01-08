<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Shipment;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterfaceFactory;
use Dhl\ShippingCore\Api\Pipeline\CreateShipmentsPipelineInterface;
use Dhl\ShippingCore\Api\Pipeline\CreateShipmentsStageInterface;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Class CreateShipmentsPipeline
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CreateShipmentsPipeline implements CreateShipmentsPipelineInterface
{
    /**
     * @var CreateShipmentsPipelineProcessor
     */
    private $pipelineProcessor;

    /**
     * @var ArtifactsContainerInterfaceFactory
     */
    private $artifactsContainerFactory;

    /**
     * @var CreateShipmentsStageInterface[]
     */
    private $stages;

    /**
     * CreateShipmentsPipeline constructor.
     *
     * @param CreateShipmentsPipelineProcessor $pipelineProcessor
     * @param ArtifactsContainerInterfaceFactory $artifactsContainerFactory
     * @param CreateShipmentsStageInterface[] $stages
     */
    public function __construct(
        CreateShipmentsPipelineProcessor $pipelineProcessor,
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
     * @param Request[] $requests
     * @return ArtifactsContainerInterface
     */
    public function run(int $storeId, array $requests): ArtifactsContainerInterface
    {
        $artifactsContainer = $this->artifactsContainerFactory->create();
        $artifactsContainer->setStoreId($storeId);

        $this->pipelineProcessor->process($requests, $artifactsContainer, $this->stages);

        return $artifactsContainer;
    }
}
