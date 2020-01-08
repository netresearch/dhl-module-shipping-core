<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\BulkShipment;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;
use Psr\Log\LoggerInterface;
use Zend_Db_Exception;

/**
 * Class OrderCollectionLoader
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class OrderCollectionLoader
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
     * @var FilterGroupBuilder
     */
    private $filterGroupBuilder;

    /**
     * @var OrderRepositoryInterface
     */
    private $orderRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * OrderCollectionLoader constructor.
     *
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param OrderRepositoryInterface $orderRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        OrderRepositoryInterface $orderRepository,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->orderRepository = $orderRepository;
        $this->logger = $logger;
    }

    /**
     * Prepare filters and load orders through the repository for bulk shipment processing.
     *
     * @param string[] $orderIds
     * @param string[] $carrierCodes
     * @return OrderSearchResultInterface|OrderInterface[]
     */
    public function load(array $orderIds, array $carrierCodes)
    {
        $orderIdFilter = $this->filterBuilder->setField(OrderInterface::ENTITY_ID)
            ->setValue($orderIds)
            ->setConditionType('in')
            ->create();

        $carrierFilters = [];
        foreach ($carrierCodes as $carrierCode) {
            $carrierFilters[]= $this->filterBuilder->setField('main_table.shipping_method')
                ->setValue("$carrierCode%")
                ->setConditionType('like')
                ->create();
        }

        // set simple filters
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteriaBuilder->addFilter($orderIdFilter);
        $searchCriteria = $searchCriteriaBuilder->create();

        // add filter groups
        $this->filterGroupBuilder->setFilters($carrierFilters);
        $carrierFilterGroup = $this->filterGroupBuilder->create();

        $filterGroups = $searchCriteria->getFilterGroups();
        // add carrier filters as one OR group
        $filterGroups[]= $carrierFilterGroup;
        $searchCriteria->setFilterGroups($filterGroups);

        try {
            return $this->orderRepository->getList($searchCriteria);
        } catch (Zend_Db_Exception $exception) {
            $this->logger->error('Could not load orders for bulk processing.', ['exception' => $exception]);
            return [];
        }
    }
}
