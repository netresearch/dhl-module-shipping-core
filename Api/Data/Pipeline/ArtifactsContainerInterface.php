<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline;

/**
 * Collect data during pipeline stage execution.
 *
 * Pipeline stages receive the request object(s) as input for modification or processing. To pass on further data
 * to subsequent stages or back the pipeline caller, the data can be added to the pipeline's artifacts container.
 * Artifacts containers need to be implemented with getters and setters depending on the data to share.
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
