<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\BulkShipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\Data\ShipmentInterface;
use Magento\Sales\Api\Data\ShipmentSearchResultInterface;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class ShipmentCollectionLoader
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShipmentCollectionLoader
{
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * ShipmentCollectionLoader constructor.
     *
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param ShipmentRepositoryInterface $shipmentRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        ShipmentRepositoryInterface $shipmentRepository,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->shipmentRepository = $shipmentRepository;
        $this->logger = $logger;
    }

    /**
     * Prepare filters and load orders through the repository for bulk shipment processing.
     *
     * @param string[] $shipmentIds
     * @return ShipmentSearchResultInterface|ShipmentInterface[]
     */
    public function load(array $shipmentIds)
    {
        $shipmentIdFilter = $this->filterBuilder->setField(ShipmentInterface::ENTITY_ID)
            ->setValue($shipmentIds)
            ->setConditionType('in')
            ->create();

        // set simple filters
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter($shipmentIdFilter);
        $searchCriteria = $searchCriteriaBuilder->create();

        try {
            return $this->shipmentRepository->getList($searchCriteria);
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error('Could not load shipments for bulk processing.', ['exception' => $exception]);
            return [];
        }
    }
}
