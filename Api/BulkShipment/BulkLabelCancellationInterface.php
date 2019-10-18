<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\BulkShipment;

use Dhl\ShippingCore\Api\Data\TrackRequest\TrackRequestInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;

/**
 * Interface BulkLabelCancellationInterface
 *
 * Service to cancel shipping labels.
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface BulkLabelCancellationInterface
{
    /**
     * Cancel shipping labels for given cancellation requests.
     *
     * @param TrackRequestInterface[] $cancelRequests
     * @return TrackResponseInterface[]
     */
    public function cancelLabels(array $cancelRequests): array;
}