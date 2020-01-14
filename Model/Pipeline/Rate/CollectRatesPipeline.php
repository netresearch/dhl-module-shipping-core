<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterfaceFactory;
use Dhl\ShippingCore\Api\Pipeline\CollectRatesPipelineInterface;
use Dhl\ShippingCore\Api\Pipeline\CollectRatesStageInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class CollectRatesPipeline
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CollectRatesPipeline implements CollectRatesPipelineInterface
{
    /**
     * @var CollectRatesPipelineProcessor
     */
    private $pipelineProcessor;

    /**
     * @var ArtifactsContainerInterfaceFactory
     */
    private $artifactsContainerFactory;

    /**
     * @var CollectRatesStageInterface[]
     */
    private $stages;

    /**
     * CollectRatesPipeline constructor.
     *
     * @param CollectRatesPipelineProcessor $pipelineProcessor
     * @param ArtifactsContainerInterfaceFactory $artifactsContainerFactory
     * @param CollectRatesStageInterface[] $stages
     */
    public function __construct(
        CollectRatesPipelineProcessor $pipelineProcessor,
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
     * @param RateRequest $request
     * @return ArtifactsContainerInterface
     * @throws LocalizedException
     */
    public function run(int $storeId, RateRequest $request): ArtifactsContainerInterface
    {
        $artifactsContainer = $this->artifactsContainerFactory->create();
        $artifactsContainer->setStoreId($storeId);

        $this->pipelineProcessor->process($request, $artifactsContainer, $this->stages);

        return $artifactsContainer;
    }
}
