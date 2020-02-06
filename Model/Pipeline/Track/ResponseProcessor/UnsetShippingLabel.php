<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Track\ResponseProcessor;

use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\TrackResponseProcessorInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Model\ResourceModel\Order\Shipment;

/**
 * Class UnsetShippingLabel
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class UnsetShippingLabel implements TrackResponseProcessorInterface
{
    /**
     * @var Shipment
     */
    private $shipmentResource;

    /**
     * UnsetShippingLabel constructor.
     *
     * @param Shipment $shipmentResource
     */
    public function __construct(Shipment $shipmentResource)
    {
        $this->shipmentResource = $shipmentResource;
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
     * Unset labels.
     *
     * Do not only remove labels of shipments which had all tracks deleted
     * but also of shipments which had the tracks only partially cancelled.
     * This is necessary because packages cannot be recreated individually.
     *
     * @param TrackResponseInterface[] $trackResponses
     * @param TrackErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $trackResponses, array $errorResponses)
    {
        $cancelledShipments = $this->getCancelledShipments($trackResponses);

        array_walk(
            $cancelledShipments,
            function (ShipmentInterface $shipment) {
                /** @var \Magento\Sales\Model\Order\Shipment $shipment */
                $shipment->setShippingLabel(null);
                $this->shipmentResource->save($shipment);
            }
        );
    }
}
