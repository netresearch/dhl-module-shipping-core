<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Model\BulkShipment\NotImplementedException;
use Dhl\ShippingCore\Model\BulkShipment\OrderCollectionLoader;
use Dhl\ShippingCore\Model\BulkShipment\ShipmentCollectionLoader;
use Dhl\ShippingCore\Model\Shipment\CancelRequestBuilder;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Psr\Log\LoggerInterface;

/**
 * Class BulkShipmentManagement
 *
 * @package Dhl\ShippingCore\Model
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class BulkShipmentManagement
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var BulkShipmentConfiguration
     */
    private $bulkConfig;

    /**
     * @var OrderCollectionLoader
     */
    private $orderCollectionLoader;

    /**
     * @var ShipmentCollectionLoader
     */
    private $shipmentCollectionLoader;

    /**
     * @var ShipOrderInterface
     */
    private $shipOrder;

    /**
     * @var CancelRequestBuilder
     */
    private $cancelRequestBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * BulkShipmentManagement constructor.
     *
     * @param ConfigInterface $config
     * @param BulkShipmentConfiguration $bulkConfig
     * @param OrderCollectionLoader $orderCollectionLoader
     * @param ShipmentCollectionLoader $shipmentCollectionLoader
     * @param ShipOrderInterface $shipOrder
     * @param CancelRequestBuilder $cancelRequestBuilder
     * @param LoggerInterface $logger
     * @param RequestFactory $requestFactory
     */
    public function __construct(
        ConfigInterface $config,
        BulkShipmentConfiguration $bulkConfig,
        OrderCollectionLoader $orderCollectionLoader,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        ShipOrderInterface $shipOrder,
        CancelRequestBuilder $cancelRequestBuilder,
        LoggerInterface $logger,
        RequestFactory $requestFactory
    ) {
        $this->config = $config;
        $this->bulkConfig = $bulkConfig;
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->shipOrder = $shipOrder;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;
    }

    /**
     * Get the first shipment with no label from shipments collection
     *
     * @param Order $order
     * @return string|null
     */
    private function getOrderShipmentId(Order $order)
    {
        $shipmentIds = [];
        foreach ($order->getShipmentsCollection()->getItems() as $item) {
            if (!$item->getShippingLabel()) {
                $shipmentIds[] = $item->getEntityId();
            }
        }

        return array_shift($shipmentIds);
    }

    /**
     * Create shipments for given order IDs.
     *
     * @param string[] $orderIds List of selected order ids
     * @return int[] Created shipment IDs by order increment ID, e.g. ['1000023' => 42, '1000024' => null]
     */
    public function createShipments(array $orderIds): array
    {
        $result = [];

        $retryFailedShipments = $this->config->isBulkRetryEnabled();

        $fnFilter = function (string $carrierCode) {
            try {
                return $this->bulkConfig->getBulkShipmentService($carrierCode);
            } catch (NotImplementedException $exception) {
                return false;
            }
        };

        $carrierCodes = array_filter($this->bulkConfig->getCarrierCodes(), $fnFilter);
        $orders = $this->orderCollectionLoader->load($orderIds, $carrierCodes);

        /** @var Order $order */
        foreach ($orders as $order) {
            if (!$order->canShip()) {
                $result[$order->getIncrementId()] = $retryFailedShipments ? $this->getOrderShipmentId($order) : null;
                continue;
            }

            try {
                $notify = $this->config->isBulkNotificationEnabled($order->getStoreId());
                $result[$order->getIncrementId()] = $this->shipOrder->execute($order->getId(), [], $notify);
            } catch (\Exception $exception) {
                $result[$order->getIncrementId()] = null;
                $this->logger->error($exception->getMessage(), ['exception' => $exception]);
            }
        }

        return $result;
    }

    /**
     * Create labels for given shipment IDs.
     *
     * @param int[] $shipmentIds
     * @return ShipmentResponseInterface[]
     */
    public function createLabels(array $shipmentIds)
    {
        /** @var \Magento\Sales\Model\ResourceModel\Order\Shipment\Collection $shipmentCollection */
        $shipmentCollection = $this->shipmentCollectionLoader->load($shipmentIds);
        $shipmentRequests = [];
        $carrierResults = [];

        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $order = $shipment->getOrder();
            $carrierCode = strtok((string) $order->getShippingMethod(), '_');

            $shipmentRequest = $this->requestFactory->create();
            $shipmentRequest->setOrderShipment($shipment);

            try {
                $this->bulkConfig->getRequestModifier($carrierCode)->modify($shipmentRequest);
            } catch (LocalizedException $exception) {
                $shipment->addComment(__('Automatic label creation failed: %1', $exception->getMessage()));
                continue;
            }

            $shipmentRequests[$carrierCode][] = $shipmentRequest;
        }

        foreach ($shipmentRequests as $carrierCode => $carrierShipmentRequests) {
            try {
                $labelService = $this->bulkConfig->getBulkShipmentService($carrierCode);
            } catch (NotImplementedException $exception) {
                $msg = "Bulk label creation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }

            $carrierResults[$carrierCode] = $labelService->createLabels($carrierShipmentRequests);
        }

        // persist labels and tracks added during api action post processing
        $shipmentCollection->save();

        if (!empty($carrierResults)) {
            // convert results per carrier to flat response
            $carrierResults = array_reduce($carrierResults, 'array_merge', []);
        }

        return $carrierResults;
    }

    /**
     * Cancel all tracks for given shipment IDs.
     *
     * @param int[] $shipmentIds
     * @return TrackResponseInterface[]
     */
    public function cancelLabels(array $shipmentIds)
    {
        $shipmentCollection = $this->shipmentCollectionLoader->load($shipmentIds);
        $carrierShipments = [];
        $carrierResults = [];

        // divide shipments by carrier code
        /** @var \Magento\Sales\Model\Order\Shipment $shipment */
        foreach ($shipmentCollection as $shipment) {
            $order = $shipment->getOrder();
            $carrierCode = strtok((string)$order->getShippingMethod(), '_');

            $carrierShipments[$carrierCode][] = $shipment;
        }

        // cancel tracks per carrier
        foreach ($carrierShipments as $carrierCode => $shipments) {
            $this->cancelRequestBuilder->setShipments($shipments);
            $cancelRequests = $this->cancelRequestBuilder->build($carrierCode);

            try {
                $labelService = $this->bulkConfig->getBulkCancellationService($carrierCode);
            } catch (NotImplementedException $exception) {
                $msg = "Bulk label cancellation is not supported by carrier '$carrierCode'";
                $this->logger->warning($msg, ['exception' => $exception]);
                continue;
            }

            $carrierResults[$carrierCode] = $labelService->cancelLabels($cancelRequests);
        }

        if (!empty($carrierResults)) {
            // convert results per carrier to flat response
            $carrierResults = array_reduce($carrierResults, 'array_merge', []);
        }

        return $carrierResults;
    }
}
