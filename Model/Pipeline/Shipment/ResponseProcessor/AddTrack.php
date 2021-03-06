<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Magento\Shipping\Model\Order\TrackFactory;

/**
 * Add track entity to shipment after api calls.
 */
class AddTrack implements ShipmentResponseProcessorInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var TrackFactory
     */
    private $trackFactory;

    /**
     * AddTrack constructor.
     * @param ConfigInterface $config
     * @param TrackFactory $trackFactory
     */
    public function __construct(ConfigInterface $config, TrackFactory $trackFactory)
    {
        $this->config = $config;
        $this->trackFactory = $trackFactory;
    }

    /**
     * Perform actions after receiving the "create shipments" response.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses)
    {
        foreach ($labelResponses as $labelResponse) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            $order = $shipment->getOrder();

            $carrierCode = strtok((string)$order->getShippingMethod(), '_');
            $carrierTitle = $this->config->getCarrierTitleByCode($carrierCode, $shipment->getStoreId());

            $track = $this->trackFactory->create();
            $track->setNumber($labelResponse->getTrackingNumber());
            $track->setCarrierCode($carrierCode);
            $track->setTitle($carrierTitle);
            $shipment->addTrack($track);
        }
    }
}
