<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline;

/**
 * Interface ArtifactsContainerInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface ArtifactsContainerInterface
{
    /**
     * Set store id for the pipeline.
     *
     * @param int $storeId
     * @return void
     */
    public function setStoreId(int $storeId);

    /**
     * Get store id for the pipeline.
     *
     * @return int
     */
    public function getStoreId(): int;
}
