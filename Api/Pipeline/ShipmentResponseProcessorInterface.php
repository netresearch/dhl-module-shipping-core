<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Pipeline;

use Dhl\ShippingCore\Api\Data\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentErrorResponseInterface;

/**
 * Post-process shipping labels and label errors as retrieved from the create shipments pipeline.
 *
 * Response processors offer a dedicated way to perform additional actions on the artifacts collected during pipeline
 * execution. The default implementation is a composite processor. There are pre-defined processors available, any
 * further processors which implement this interface may be created and added via configuration.
 *
 * @see CreateShipmentsPipelineInterface
 *
 * @api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface ShipmentResponseProcessorInterface
{
    /**
     * Perform actions after receiving the "create shipments" response.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses);
}
