<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Cron;

use Dhl\ShippingCore\Api\BulkShipmentConfigurationInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentResponseInterface;
use Dhl\ShippingCore\Cron\AutoCreate\OrderCollectionLoader;
use Dhl\ShippingCore\Cron\AutoCreate\OrderCollectionLoader\AutoCreateDisabledException;
use Dhl\ShippingCore\Model\BulkShipmentManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Psr\Log\LoggerInterface;

/**
 * Cron entry point for automatic shipment creation and label retrieval.
 *
 * @package Dhl\ShippingCore\Cron
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class AutoCreate
{
    /**
     * @var OrderCollectionLoader
     */
    private $orderCollectionLoader;

    /**
     * @var BulkShipmentManagement
     */
    private $bulkShipmentManagement;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var BulkShipmentConfigurationInterface[]
     */
    private $configurations;

    /**
     * AutoCreate constructor.
     *
     * @param OrderCollectionLoader $orderCollectionLoader
     * @param BulkShipmentManagement $bulkShipmentManagement
     * @param LoggerInterface $logger
     * @param BulkShipmentConfigurationInterface[] $configurations
     */
    public function __construct(
        OrderCollectionLoader $orderCollectionLoader,
        BulkShipmentManagement $bulkShipmentManagement,
        LoggerInterface $logger,
        array $configurations = []
    ) {
        $this->orderCollectionLoader = $orderCollectionLoader;
        $this->bulkShipmentManagement = $bulkShipmentManagement;
        $this->logger = $logger;
        $this->configurations = $configurations;
    }

    /**
     * Collect orders with pending labels and try to create shipments for them via the corresponding API.
     */
    public function execute()
    {
        $getCarrierCode = static function (BulkShipmentConfigurationInterface $bulkConfiguration) {
            return $bulkConfiguration->getCarrierCode();
        };
        $carrierCodes = array_map($getCarrierCode, $this->configurations);

        try {
            /** @var \Magento\Framework\Data\Collection\AbstractDb $orderCollection */
            $orderCollection = $this->orderCollectionLoader->load($carrierCodes);
        } catch (AutoCreateDisabledException $exception) {
            $this->logger->debug($exception->getLogMessage());
            return;
        } catch (NoSuchEntityException $exception) {
            $this->logger->warning($exception->getLogMessage());
            return;
        }

        $orderIds = $orderCollection->getColumnValues(OrderInterface::ENTITY_ID);
        $orderIncrementIds = $orderCollection->getColumnValues(OrderInterface::INCREMENT_ID);

        $shipmentIds = $this->bulkShipmentManagement->createShipments($orderIds);

        // extract successfully created shipments, inform about creation errors
        $shipmentIds = array_filter($shipmentIds);
        $failed = array_diff($orderIncrementIds, array_keys($shipmentIds));
        if (!empty($failed)) {
            $message = sprintf('Shipment(s) for the order(s) %s could not be created.', implode(', ', $failed));
            $this->logger->error($message);
        }

        // create labels and tracks for above shipments
        $result = $this->bulkShipmentManagement->createLabels($shipmentIds);

        // check result, inform about created and failed labels/tracks
        $processResult = function (array $incrementIds, ShipmentResponseInterface $shipmentResponse) {
            /** @var \Magento\Sales\Model\Order\Shipment $shipment */
            $shipment = $shipmentResponse->getSalesShipment();
            $orderIncrementId = $shipment->getOrder()->getIncrementId();

            if ($shipmentResponse instanceof LabelResponseInterface
                && !empty($shipment->getShippingLabel())
                && !empty($shipment->getTracks())
            ) {
                // collect successfully created labels
                $incrementIds['success'][] = $orderIncrementId;
            } else {
                // collect label errors
                $incrementIds['error'][] = $orderIncrementId;
                if ($shipmentResponse instanceof ShipmentErrorResponseInterface) {
                    // add error message if details are available
                    $message = sprintf('Order %s: %s.', $orderIncrementId, $shipmentResponse->getErrors());
                    $this->logger->error($message);
                }
            }

            return $incrementIds;
        };

        $labelResponses = array_reduce($result, $processResult, ['success' => [], 'error' => []]);

        if (!empty($labelResponses['success'])) {
            // positive webservice responses
            $message = sprintf(
                'Shipping label(s) for the order(s) %s were successfully created.',
                implode(', ', $labelResponses['success'])
            );
            $this->logger->info($message);
        }

        if (!empty($labelResponses['error'])) {
            // negative webservice responses
            $message = sprintf(
                'Shipping label(s) for the order(s) %s could not be created.',
                implode(', ', $labelResponses['error'])
            );
            $this->logger->error($message);
        }

        $autoCreateErrors = array_diff(array_keys($shipmentIds), $labelResponses['success'], $labelResponses['error']);
        if (!empty($autoCreateErrors)) {
            // no webservice responses, errors during request preparation
            $message = sprintf(
                'Shipping label(s) for the order(s) %s could not be requested. Please review shipment comments.',
                implode(', ', $autoCreateErrors)
            );
            $this->logger->error($message);
        }
    }
}
