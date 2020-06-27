<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track\ResponseProcessor;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\TrackResponseProcessorInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\Order\Shipment\TrackRepository;
use Psr\Log\LoggerInterface;

class DeleteTrack implements TrackResponseProcessorInterface
{
    /**
     * @var TrackRepository
     */
    private $trackRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * DeleteTrack constructor.
     *
     * @param TrackRepository $trackRepository
     * @param LoggerInterface $logger
     */
    public function __construct(TrackRepository $trackRepository, LoggerInterface $logger)
    {
        $this->trackRepository = $trackRepository;
        $this->logger = $logger;
    }

    /**
     * Collect shipments which had at least one track successfully cancelled.
     *
     * @param TrackResponseInterface[] $trackResponses
     * @return ShipmentInterface[]
     */
    private function getCancelledShipments(array $trackResponses): array
    {
        $shipments = [];

        foreach ($trackResponses as $trackResponse) {
            $shipment = $trackResponse->getSalesShipment();
            if ($shipment !== null) {
                $shipments[$shipment->getEntityId()] = $shipment;
            }
        }

        return $shipments;
    }

    /**
     * Delete track entities.
     *
     * Do not only delete successfully cancelled tracks but also failed tracks
     * which belong to a shipment that had at least one track successfully
     * cancelled. This is necessary because packages cannot be recreated individually.
     *
     * Note that there is not necessarily a track entity persisted for a given shipment number.
     *
     * @param TrackResponseInterface[] $trackResponses Shipment cancellation responses
     * @param TrackErrorResponseInterface[] $errorResponses Shipment cancellation errors
     * @see \Magento\Shipping\Model\Carrier\AbstractCarrierOnline::rollBack
     */
    public function processResponse(array $trackResponses, array $errorResponses)
    {
        $cancelledShipments = $this->getCancelledShipments($trackResponses);

        /** @var TrackResponseInterface[] $responses */
        $responses = array_merge($trackResponses, $errorResponses);
        foreach ($responses as $response) {
            $track = $response->getSalesTrack();
            if ($track === null) {
                // track was not yet persisted, nothing to delete.
                continue;
            }

            $shipment = $response->getSalesShipment();
            if (!$shipment || !isset($cancelledShipments[$shipment->getEntityId()])) {
                // shipment was not yet persisted or has no cancelled tracks.
                continue;
            }

            try {
                $this->trackRepository->delete($track);
            } catch (CouldNotDeleteException $exception) {
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }
    }
}
