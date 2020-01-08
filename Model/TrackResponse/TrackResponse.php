<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\TrackResponse;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

/**
 * TrackResponse
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
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
