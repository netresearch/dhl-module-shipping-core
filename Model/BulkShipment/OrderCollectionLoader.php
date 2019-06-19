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
use Zend_Db_Exception;

/**
 * Class OrderCollectionLoader
 *
 * @package Dhl\ShippingCore\Model
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
     * OrderCollectionLoader constructor.
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->orderRepository = $orderRepository;
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
            //todo(nr): log exception
            return [];
        }
    }
}
