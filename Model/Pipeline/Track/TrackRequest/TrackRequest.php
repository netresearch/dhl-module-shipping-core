<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track\TrackRequest;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackRequest\TrackRequestInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentTrackInterface;

class TrackRequest implements TrackRequestInterface
{
    /**
     * @var int
     */
    private $storeId;

    /**
     * @var string
     */
    private $trackNumber;

    /**
     * @var ShipmentInterface
     */
    private $salesShipment;

    /**
     * @var ShipmentTrackInterface
     */
    private $salesTrack;

    /**
     * TrackRequest constructor.
     *
     * @param int $storeId
     * @param string $trackNumber
     * @param ShipmentInterface|null $salesShipment
     * @param ShipmentTrackInterface|null $salesTrack
     */
    public function __construct(
        int $storeId,
        string $trackNumber,
        ShipmentInterface $salesShipment = null,
        ShipmentTrackInterface $salesTrack = null
    ) {
        $this->storeId = $storeId;
        $this->trackNumber = $trackNumber;
        $this->salesShipment = $salesShipment;
        $this->salesTrack = $salesTrack;
    }

    /**
     * Obtain store id
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return $this->storeId;
    }

    /**
     * Obtain tracking number
     *
     * @return string
     */
    public function getTrackNumber(): string
    {
        return $this->trackNumber;
    }

    /**
     * @return ShipmentInterface|null
     */
    public function getSalesShipment()
    {
        return $this->salesShipment;
    }

    /**
     * @return ShipmentTrackInterface|null
     */
    public function getSalesTrack()
    {
        return $this->salesTrack;
    }
}
