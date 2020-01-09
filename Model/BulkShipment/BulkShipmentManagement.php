<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\BulkShipment;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Model\LabelStatus\LabelStatusProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order;
use Magento\Shipping\Model\Shipment\RequestFactory;
use Psr\Log\LoggerInterface;

/**
 * Class BulkShipmentManagement
 *
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
     * @var LabelStatusProvider
     */
    private $labelStatusProvider;

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
     * @param LabelStatusProvider $labelStatusProvider
     */
    public function __construct(
        ConfigInterface $config,
        BulkShipmentConfiguration $bulkConfig,
        OrderCollectionLoader $orderCollectionLoader,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        ShipOrderInterface $shipOrder,
        CancelRequestBuilder $cancelRequestBuilder,
        LoggerInterface $logger,
        RequestFactory $requestFactory,
        LabelStatusProvider $labelStatusProvider
    ) {
        $this->config = $config;
        $this->bulkConfig = $bulkConfig;
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->shipOrder = $shipOrder;
        $this->cancelRequestBuilder = $cancelRequestBuilder;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;
        $this->labelStatusProvider = $labelStatusProvider;
    }

    /**
     * Create shipments for given order IDs.
     *
     * @param string[] $orderIds List of selected order ids
     * @return int[][] Created shipment IDs by order increment ID, e.g. ['1000023' => [42, 43], '1000024' => []]
     */
    public function createShipments(array $orderIds): array
    {
        $result = [];

        $retryFailedShipments = $this->config->isBulkRetryEnabled();

        $fnFilter = function (string $carrierCode) {
            try {
                return $this->bulkConfig->getBulkShipmentService($carrierCode);
            } catch (LocalizedException $exception) {
                return false;
            }
        };

        $carrierCodes = array_filter($this->bulkConfig->getCarrierCodes(), $fnFilter);
        $orders = $this->orderCollectionLoader->load($orderIds, $carrierCodes);
        $ordersLabelStatus = $this->labelStatusProvider->getLabelStatus($orderIds);

        /** @var Order $order */
        foreach ($orders as $order) {
            $notify = $this->config->isBulkNotificationEnabled($order->getStoreId());
            $shipmentsCollection = $order->getShipmentsCollection()
                ->addFieldToFilter(ShipmentInterface::SHIPPING_LABEL, ['null' => true]);

            $labelStatus = $ordersLabelStatus[$order->getId()] ?? null;
            if ($retryFailedShipments || $labelStatus !== LabelStatusManagementInterface::LABEL_STATUS_FAILED) {
                $shipmentIds = $shipmentsCollection->getAllIds();
            } else {
                $shipmentIds = [];
            }

            if ($order->canShip()) {
                try {
                    $shipmentId = $this->shipOrder->execute($order->getId(), [], $notify);
                    $shipmentIds[]= $shipmentId;
                } catch (\Exception $exception) {
                    $this->logger->error($exception->getMessage(), ['exception' => $exception]);
                }
            }

            $result[$order->getIncrementId()] = $shipmentIds;
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
            } catch (LocalizedException $exception) {
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
            } catch (LocalizedException $exception) {
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
