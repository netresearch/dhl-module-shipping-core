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
 * Interface CollectRatesPipelineInterface
 *
 * @api
 * @package Dhl\ShippingCore\Webservice
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
