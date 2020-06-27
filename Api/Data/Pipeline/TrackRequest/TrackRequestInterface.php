<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

/**
 * @api
 */
interface TrackRequestInterface
{
    /**
     * Obtain store id
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * Obtain tracking number
     *
     * @return string
     */
    public function getTrackNumber(): string;

    /**
     * @return ShipmentInterface|null
     */
    public function getSalesShipment();

    /**
     * @return ShipmentTrackInterface|null
     */
    public function getSalesTrack();
}
