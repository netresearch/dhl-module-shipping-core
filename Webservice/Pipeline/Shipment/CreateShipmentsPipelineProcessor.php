<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Shipment;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Dhl\ShippingCore\Api\Pipeline\CreateShipmentsStageInterface;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Class CreateShipmentsPipelineProcessor
 *
 * @package Dhl\ShippingCore\Webservice
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CreateShipmentsPipelineProcessor
{
    /**
     * Execute stages, pass previous result to next stage.
     *
     * @param Request[] $requests
     * @param ArtifactsContainerInterface $artifactsContainer
     * @param CreateShipmentsStageInterface[] $stages
     * @return Request[]
     */
    public function process(array $requests, ArtifactsContainerInterface $artifactsContainer, array $stages): array
    {
        foreach ($stages as $stage) {
            $requests = $stage->execute($requests, $artifactsContainer);
        }

        return $requests;
    }
}
