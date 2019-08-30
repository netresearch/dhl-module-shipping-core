<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\BulkShipmentConfigurationInterface;
use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentResponseInterface;
use Dhl\ShippingCore\Model\BulkShipment\OrderCollectionLoader;
use Dhl\ShippingCore\Model\BulkShipment\ShipmentCollectionLoader;
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
     * @var BulkShipmentConfigurationInterface[]
     */
    private $configurations;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RequestFactory
     */
    private $requestFactory;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * BulkShipmentProcessor constructor.
     * @param OrderCollectionLoader $orderCollectionLoader
     * @param ShipmentCollectionLoader $shipmentCollectionLoader
     * @param ShipOrderInterface $shipOrder
     * @param LoggerInterface $logger
     * @param RequestFactory $requestFactory
     * @param ConfigInterface $config
     * @param BulkShipmentConfigurationInterface[] $configurations
     */
    public function __construct(
        OrderCollectionLoader $orderCollectionLoader,
        ShipmentCollectionLoader $shipmentCollectionLoader,
        ShipOrderInterface $shipOrder,
        LoggerInterface $logger,
        RequestFactory $requestFactory,
        ConfigInterface $config,
        array $configurations = []
    ) {
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->shipmentCollectionLoader = $shipmentCollectionLoader;
        $this->shipOrder = $shipOrder;
        $this->logger = $logger;
        $this->requestFactory = $requestFactory;
        $this->config = $config;
        $this->configurations = $configurations;
    }

    /**
     * Load bulk shipment configuration for the given carrier code.
     *
     * @param string $carrierCode
     * @return BulkShipmentConfigurationInterface
     * @throws \InvalidArgumentException
     */
    private function getCarrierConfigurationByCode(string $carrierCode): BulkShipmentConfigurationInterface
    {
        foreach ($this->configurations as $configuration) {
            if ($configuration->getCarrierCode() === $carrierCode) {
                return $configuration;
            }
        }

        throw new \InvalidArgumentException("Bulk shipment configuration for carrier $carrierCode is not available.");
    }

    /**
     * Load bulk shipment configuration for the given order.
     *
     * @param Order $order
     * @return BulkShipmentConfigurationInterface
     * @throws \InvalidArgumentException
     */
    private function getCarrierConfiguration(Order $order): BulkShipmentConfigurationInterface
    {
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');
        return $this->getCarrierConfigurationByCode($carrierCode);
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

        $carrierCodes = array_map(function (BulkShipmentConfigurationInterface $bulkConfiguration) {
            return $bulkConfiguration->getCarrierCode();
        }, $this->configurations);

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
            $configuration = $this->getCarrierConfiguration($order);

            $shipmentRequest = $this->requestFactory->create();
            $shipmentRequest->setOrderShipment($shipment);

            try {
                $configuration->getRequestModifier()->modify($shipmentRequest);
            } catch (LocalizedException $exception) {
                $shipment->addComment(__('Automatic label creation failed: %1', $exception->getMessage()));
                continue;
            }

            $shipmentRequests[$configuration->getCarrierCode()][] = $shipmentRequest;
        }

        foreach ($shipmentRequests as $carrierCode => $carrierShipmentRequests) {
            $labelService = $this->getCarrierConfigurationByCode($carrierCode)->getLabelService();
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
}
