<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture;

use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\AddressInterface;
use Dhl\ShippingCore\Test\Integration\Fixture\Data\ProductInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Sales\Api\Data\InvoiceItemCreationInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentItemCreationInterface;
use Magento\Sales\Api\Data\ShipmentTrackCreationInterfaceFactory;
use Magento\Sales\Api\InvoiceOrderInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Api\ShipOrderInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\TestFramework\Helper\Bootstrap;

/**
 * Class ShipmentFixture
 *
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShipmentFixture
{
    /**
     * Creates an order with a singular shipment. Label depends on $trackingNumbers parameters.
     *
     * @param AddressInterface $recipientData
     * @param ProductInterface[] $productData
     * @param string $shippingMethod
     * @param string[] $trackingNumbers If empty no label will be set on the shipment
     * @param bool $invoice
     * @return ShipmentInterface
     * @throws \Exception
     */
    public static function createShipment(
        AddressInterface $recipientData,
        array $productData,
        string $shippingMethod,
        array $trackingNumbers = [],
        bool $invoice = false
    ): ShipmentInterface {
        /** @var \Magento\Sales\Model\Order $order */
        $order = OrderFixture::createOrder($recipientData, $productData, $shippingMethod);

        $carrierCode = strtok($shippingMethod, '_');
        $tracks = array_map(
            function (string $trackingNumber) use ($carrierCode) {
                /** @var ShipmentTrackCreationInterfaceFactory $trackFactory */
                $trackFactory = Bootstrap::getObjectManager()->get(ShipmentTrackCreationInterfaceFactory::class);
                $track = $trackFactory->create();
                $track->setCarrierCode($carrierCode);
                $track->setTitle($carrierCode);
                $track->setTrackNumber($trackingNumber);

                return $track;
            },
            $trackingNumbers
        );

        /** @var ShipOrderInterface $shipOrder */
        $shipOrder = Bootstrap::getObjectManager()->create(ShipOrderInterface::class);
        $shipmentId = $shipOrder->execute($order->getEntityId(), [], false, false, null, $tracks);

        if ($invoice) {
            /** @var InvoiceOrderInterface $invoiceOrder */
            $invoiceOrder = Bootstrap::getObjectManager()->create(InvoiceOrderInterface::class);
            $invoiceOrder->execute($order->getEntityId());
        }

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = Bootstrap::getObjectManager()->get(ShipmentRepositoryInterface::class);
        $shipment = $shipmentRepository->get($shipmentId);

        if (!empty($tracks)) {
            // if tracks were added, also add shipping label and label status
            $shipment->setShippingLabel('%PDF-1.4');
            $shipmentRepository->save($shipment);

            /** @var LabelStatusManagementInterface $labelStatusManagement */
            $labelStatusManagement = Bootstrap::getObjectManager()->get(LabelStatusManagementInterface::class);
            $labelStatusManagement->setLabelStatusProcessed($order);
        }

        return $shipment;
    }

    /**
     * Creates a shipment with no label. Sets label status "failed".
     *
     * @param AddressInterface $recipientData
     * @param array $productData
     * @param string $shippingMethod
     * @return Shipment
     * @throws \Exception
     */
    public static function createFailedShipment(
        AddressInterface $recipientData,
        array $productData,
        string $shippingMethod
    ) {
        /** @var Shipment $shipment */
        $shipment = self::createShipment($recipientData, $productData, $shippingMethod);

        /** @var LabelStatusManagementInterface $labelStatusManagement */
        $labelStatusManagement = Bootstrap::getObjectManager()->get(LabelStatusManagementInterface::class);
        $labelStatusManagement->setLabelStatusFailed($shipment->getOrder());

        return $shipment;
    }

    /**
     * Creates an order and a shipment without label for each item in $productData.
     *
     * @param AddressInterface $recipientData
     * @param ProductInterface[] $productData
     * @param string $carrierCode
     * @param bool $invoice
     * @return ShipmentInterface[]
     * @throws \Exception
     */
    public static function createPartialShipments(
        AddressInterface $recipientData,
        array $productData,
        string $carrierCode,
        bool $invoice = false
    ): array {
        if (count($productData) < 2) {
            throw new \Exception('Partial shipments require more than 1 product.');
        }

        $shipmentIds = [];

        /** @var \Magento\Sales\Model\Order $order */
        $order = OrderFixture::createOrder($recipientData, $productData, $carrierCode);

        /** @var OrderItemInterface $item */
        foreach ($order->getAllVisibleItems() as $item) {
            /** @var ShipOrderInterface $shipOrder */
            $shipOrder = Bootstrap::getObjectManager()->create(ShipOrderInterface::class);
            $shipmentItem = Bootstrap::getObjectManager()->create(ShipmentItemCreationInterface::class);
            $shipmentItem->setQty($item->getQtyOrdered());
            $shipmentItem->setOrderItemId($item->getItemId());

            $shipmentIds[] = $shipOrder->execute($order->getEntityId(), [$shipmentItem]);

            if ($invoice) {
                /** @var InvoiceItemCreationInterface $item */
                $invoiceItem = Bootstrap::getObjectManager()->create(InvoiceItemCreationInterface::class);
                $invoiceItem->setQty($item->getQtyShipped());
                $invoiceItem->setOrderItemId($item->getItemId());

                /** @var InvoiceOrderInterface $invoiceOrder */
                $invoiceOrder = Bootstrap::getObjectManager()->create(InvoiceOrderInterface::class);
                $invoiceOrder->execute($order->getEntityId(), false, [$invoiceItem]);
            }
        }

        /** @var FilterBuilder $filterBuilder */
        $filterBuilder = Bootstrap::getObjectManager()->get(FilterBuilder::class);
        $filterBuilder->setField(ShipmentInterface::ENTITY_ID);
        $filterBuilder->setConditionType('in');
        $filterBuilder->setValue($shipmentIds);

        /** @var SearchCriteriaBuilder $searchCriteriaBuilder */
        $searchCriteriaBuilder = Bootstrap::getObjectManager()->get(SearchCriteriaBuilder::class);
        $searchCriteriaBuilder->addFilter($filterBuilder->create());

        /** @var ShipmentRepositoryInterface $shipmentRepository */
        $shipmentRepository = Bootstrap::getObjectManager()->get(ShipmentRepositoryInterface::class);

        return $shipmentRepository->getList($searchCriteriaBuilder->create())->getItems();
    }
}
