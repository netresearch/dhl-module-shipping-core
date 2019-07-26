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
 * Interface CollectRatesStageInterface
 *
 * @package Dhl\ShippingCore\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface CollectRatesStageInterface
{
    /**
     * Perform action on given rate request.
     *
     * @param RateRequest $request
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return void
     * @throws LocalizedException
     */
    public function execute(RateRequest $request, ArtifactsContainerInterface $artifactsContainer);
}
