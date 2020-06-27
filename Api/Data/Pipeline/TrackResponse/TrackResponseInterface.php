<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

/**
 * @api
 */
interface TrackResponseInterface
{
    const TRACK_NUMBER = 'track_number';
    const SALES_SHIPMENT = 'sales_shipment';
    const SALES_TRACK = 'sales_track';

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
