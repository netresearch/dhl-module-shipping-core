<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * Collect rates from a web service by passing the Magento rates request through multiple configurable stages.
 *
 * When shipping methods are to be prepared for placing an order, Magento provides all relevant quoting data to the
 * carriers as a "rate request" object. Pipeline stages process and transform the request to become suitable for sending
 * it to the carrier-specific web service, then send and transform it back to application data. The result of running
 * the pipeline is the artifacts container where all stages add their output data to. Consequently, the overall pipeline
 * result (i.e. the carrier's products which are suitable for the given cart) can afterwards be obtained from the
 * artifacts container.
 *
 * @see ArtifactsContainerInterface
 * @see CollectRatesStageInterface
 * @see RateResponseProcessorInterface
 *
 * @api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface CollectRatesPipelineInterface
{
    /**
     * Initialize pipeline and execute configured stages.
     *
     * @param int $storeId
     * @param RateRequest $request
     * @return ArtifactsContainerInterface
     * @throws LocalizedException
     */
    public function run(int $storeId, RateRequest $request): ArtifactsContainerInterface;
}
