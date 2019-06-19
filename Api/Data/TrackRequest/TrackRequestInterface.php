<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\TrackRequest;

use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

/**
 * Interface TrackRequestInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
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
