<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Track\ResponseProcessor;

use Dhl\ShippingCore\Api\Data\TrackResponse\TrackErrorResponseInterface;
use Dhl\ShippingCore\Api\Data\TrackResponse\TrackResponseInterface;
use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Api\Pipeline\TrackResponseProcessorInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class UpdateLabelStatus
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class UpdateLabelStatus implements TrackResponseProcessorInterface
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
     * UpdateLabelStatus constructor.
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
     * Check if no shipments, apart from the current shipment, have a shipping label.
     *
     * The current shipment will have its label update persisted later in the process.
     *
     * @param ShipmentInterface|Shipment $currentShipment
     * @return bool
     */
    private function isShippingPending(ShipmentInterface $currentShipment): bool
    {
        $orderIdFilter = $this->filterBuilder
            ->setField(ShipmentInterface::ORDER_ID)
            ->setValue($currentShipment->getOrderId())
            ->setConditionType('eq')
            ->create();
        $shippingLabelFilter = $this->filterBuilder
            ->setField(ShipmentInterface::SHIPPING_LABEL)
            ->setConditionType('notnull')
            ->create();
        $shipmentIdFilter = $this->filterBuilder->setField(ShipmentInterface::ENTITY_ID)
            ->setValue((int) $currentShipment->getId())
            ->setConditionType('neq')
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
     * Mark orders with cancelled shipments "pending" or "partial".
     *
     * @param TrackResponseInterface[] $trackResponses Shipment cancellation responses
     * @param TrackErrorResponseInterface[] $errorResponses Shipment cancellation errors
     */
    public function processResponse(array $trackResponses, array $errorResponses)
    {
        foreach ($trackResponses as $trackResponse) {
            /** @var Shipment $shipment */
            $shipment = $trackResponse->getSalesShipment();
            if (!$shipment) {
                continue;
            }

            if ($this->isShippingPending($shipment)) {
                $this->labelStatusManagement->setLabelStatusPending($shipment->getOrder());
            } else {
                $this->labelStatusManagement->setLabelStatusPartial($shipment->getOrder());
            }
        }
    }
}
