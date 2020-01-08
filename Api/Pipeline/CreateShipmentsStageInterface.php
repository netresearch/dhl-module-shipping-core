<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Dhl\ShippingCore\Api\Data\Pipeline\ArtifactsContainerInterface;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Perform action on shipment requests as part of running the create shipments pipeline.
 *
 * A pipeline is composed of a sequence of configured stages. One stage performs a certain task on the request object,
 * e.g. validation, transformation, mapping, sending, etc. The pipeline passes an artifacts container into all the
 * stages to store intermediate results.
 *
 * @see ArtifactsContainerInterface
 * @see CreateShipmentsPipelineInterface
 *
 * @api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface CreateShipmentsStageInterface
{
    /**
     * Perform action on given shipment requests.
     *
     * @param Request[] $requests
     * @param ArtifactsContainerInterface $artifactsContainer
     * @return Request[]
     */
    public function execute(array $requests, ArtifactsContainerInterface $artifactsContainer): array;
}
