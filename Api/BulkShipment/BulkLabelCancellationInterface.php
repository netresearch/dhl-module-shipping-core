<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\BulkShipment;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;

/**
 * Interface BulkLabelCancellationInterface
 *
 * Service to cancel shipping labels.
 *
 * @api
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
