<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment\ResponseProcessor;

use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\LabelResponseInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Api\Pipeline\ShipmentResponseProcessorInterface;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Item;
use Magento\Sales\Model\Order\Shipment;

class UpdateLabelStatus implements ShipmentResponseProcessorInterface
{
    /**
     * @var LabelStatusManagementInterface
     */
    private $labelStatusManagement;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var ShipmentRepositoryInterface
     */
    private $shipmentRepository;

    /**
     * LabelStatusProcessor constructor.
     *
     * @param LabelStatusManagementInterface $labelStatusManagement
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param ShipmentRepositoryInterface $shipmentRepository
     */
    public function __construct(
        LabelStatusManagementInterface $labelStatusManagement,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        ShipmentRepositoryInterface $shipmentRepository
    ) {
        $this->labelStatusManagement = $labelStatusManagement;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->shipmentRepository = $shipmentRepository;
    }

    /**
     * Check if all shipments, apart from the current shipment, have a shipping label.
     *
     * - If the label is created through packaging popup, then the shipment is not yet persisted
     * - If the label is created through bulk action, then the shipment is already persisted
     *
     * The current shipment will have its label persisted later in the process.
     *
     * @param int $orderId
     * @param int[] $orderShipmentIds
     * @return bool
     */
    private function isShippingCompleted(int $orderId, array $orderShipmentIds): bool
    {
        $orderIdFilter = $this->filterBuilder
            ->setField(ShipmentInterface::ORDER_ID)
            ->setValue($orderId)
            ->setConditionType('eq')
            ->create();
        $shippingLabelFilter = $this->filterBuilder
            ->setField(ShipmentInterface::SHIPPING_LABEL)
            ->setConditionType('null')
            ->create();
        $shipmentIdFilter = $this->filterBuilder->setField(ShipmentInterface::ENTITY_ID)
                                                ->setValue($orderShipmentIds)
                                                ->setConditionType('nin')
                                                ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter($orderIdFilter);
        $searchCriteriaBuilder->addFilter($shippingLabelFilter);
        $searchCriteriaBuilder->addFilter($shipmentIdFilter);
        $searchCriteria = $searchCriteriaBuilder->create();

        $searchResult = $this->shipmentRepository->getList($searchCriteria);

        return ($searchResult->getTotalCount() === 0);
    }

    /**
     * Check if all order items are assigned to shipments.
     *
     * @param Shipment $shipment
     * @return bool
     */
    private function isOrderShipped(Shipment $shipment): bool
    {
        $fnCollectItems = function (array $orderItems, Item $orderItem) {
            if (($orderItem->getProductType() === Type::TYPE_CODE)
                && ((int) $orderItem->getProductOptionByCode('shipment_type') === AbstractType::SHIPMENT_SEPARATELY)
            ) {
                $orderItems = array_merge($orderItems, $orderItem->getChildrenItems());
            } else {
                $orderItems[]= $orderItem;
            }

            return $orderItems;
        };

        $fnSumOrdered = function ($qtyOrdered, Item $orderItem) {
            $qtyOrdered += $orderItem->getQtyOrdered();
            return $qtyOrdered;
        };

        $fnSumShipped = function ($qtyShipped, Item $orderItem) {
            if ($orderItem->getIsVirtual()) {
                $qtyShipped += $orderItem->getQtyOrdered();
            } else {
                $qtyShipped += $orderItem->getQtyShipped();
            }

            return $qtyShipped;
        };

        $orderItems = array_reduce($shipment->getOrder()->getAllVisibleItems(), $fnCollectItems, []);
        $qtyOrdered = array_reduce($orderItems, $fnSumOrdered, 0);
        $qtyShipped = array_reduce($orderItems, $fnSumShipped, 0);

        return ($qtyOrdered === $qtyShipped);
    }

    /**
     * Mark orders' label status according to the webservice operation result.
     *
     * @param LabelResponseInterface[] $labelResponses
     * @param ShipmentErrorResponseInterface[] $errorResponses
     */
    public function processResponse(array $labelResponses, array $errorResponses)
    {
        foreach ($errorResponses as $errorResponse) {
            /** @var Shipment $shipment */
            $shipment = $errorResponse->getSalesShipment();
            $order = $shipment->getOrder();

            $this->labelStatusManagement->setLabelStatusFailed($order);
        }

        $shipmentIds = array_map(
            static function (LabelResponseInterface $labelResponse) {
                return $labelResponse->getSalesShipment()->getEntityId();
            },
            $labelResponses
        );

        foreach ($labelResponses as $labelResponse) {
            /** @var Shipment $shipment */
            $shipment = $labelResponse->getSalesShipment();
            if (!$shipment) {
                continue;
            }

            $orderId = (int) $shipment->getOrder()->getId();
            if ($this->isOrderShipped($shipment)
                && $this->isShippingCompleted($orderId, $shipmentIds)) {
                // all shippable items are assigned to a shipment and all shipments have labels.
                $this->labelStatusManagement->setLabelStatusProcessed($shipment->getOrder());
            } else {
                // some items are not assigned to a shipment or shipments are missing labels.
                $this->labelStatusManagement->setLabelStatusPartial($shipment->getOrder());
            }
        }
    }
}
