<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;

/**
 * Interface TrackResponseProcessorInterface
 *
 * Perform arbitrary actions after api calls.
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface TrackResponseProcessorInterface
{
    /**
     * Perform actions after receiving the "delete shipments" response.
     *
     * @param TrackResponseInterface[] $trackResponses Shipment cancellation responses
     * @param TrackErrorResponseInterface[] $errorResponses Shipment cancellation errors
     */
    public function processResponse(array $trackResponses, array $errorResponses);
}
