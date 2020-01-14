<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Pipeline\CollectRatesStageInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Class CollectRatesPipelineProcessor
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CollectRatesPipelineProcessor
{
    /**
     * Execute stages, pass previous result to next stage.
     *
     * @param RateRequest $request
     * @param ArtifactsContainerInterface $artifactsContainer
     * @param CollectRatesStageInterface[] $stages
     * @return void
     * @throws LocalizedException
     */
    public function process(RateRequest $request, ArtifactsContainerInterface $artifactsContainer, array $stages)
    {
        foreach ($stages as $stage) {
            $stage->execute($request, $artifactsContainer);
        }
    }
}
