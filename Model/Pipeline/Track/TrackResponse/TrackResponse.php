<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track\TrackResponse;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

class TrackResponse extends DataObject implements TrackResponseInterface
{
    /**
     * Obtain tracking number
     *
     * @return string
     */
    public function getTrackNumber(): string
    {
        return $this->getData(self::TRACK_NUMBER);
    }

    /**
     * @return ShipmentInterface|null
     */
    public function getSalesShipment()
    {
        return $this->getData(self::SALES_SHIPMENT);
    }

    /**
     * @return ShipmentTrackInterface|null
     */
    public function getSalesTrack()
    {
        return $this->getData(self::SALES_TRACK);
    }
}
