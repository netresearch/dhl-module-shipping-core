<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Cron\AutoCreate;

use Dhl\ShippingCore\Cron\AutoCreate\OrderCollectionLoader\AutoCreateDisabledException;
use Dhl\ShippingCore\Model\Config\CronConfig;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\FilterGroupBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\Data\OrderSearchResultInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

class OrderCollectionLoader
{
    /**
     * @var CronConfig
     */
    private $cronConfig;

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
     *
     * @param CronConfig $cronConfig
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param FilterGroupBuilder $filterGroupBuilder
     * @param OrderRepositoryInterface $orderRepository
     */
    public function __construct(
        CronConfig $cronConfig,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        FilterGroupBuilder $filterGroupBuilder,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->cronConfig = $cronConfig;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->filterGroupBuilder = $filterGroupBuilder;
        $this->orderRepository = $orderRepository;
    }

    /**
     * Prepare filters and load orders through the repository for cron processing.
     *
     * @param string[] $carrierCodes
     * @return OrderSearchResultInterface|OrderInterface[]
     * @throws AutoCreateDisabledException
     * @throws NoSuchEntityException
     */
    public function load(array $carrierCodes): OrderSearchResultInterface
    {
        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();

        $storeIds = $this->cronConfig->getAutoCreateStores();
        if (empty($storeIds)) {
            throw new AutoCreateDisabledException(__('No orders found (automatic label retrieval is not enabled).'));
        }

        $storeFilter = $this->filterBuilder
            ->setField('main_table.' . OrderInterface::STORE_ID)
            ->setValue($storeIds)
            ->setConditionType('in')
            ->create();

        $orderStatus = $this->cronConfig->getAutoCreateOrderStatus();
        $statusFilter = $this->filterBuilder
            ->setField('main_table.' . OrderInterface::STATUS)
            ->setValue($orderStatus)
            ->setConditionType('in')
            ->create();

        $searchCriteriaBuilder->addFilter($storeFilter);
        $searchCriteriaBuilder->addFilter($statusFilter);

        // Create filter groups from filters
        $searchCriteria = $searchCriteriaBuilder->create();
        $filterGroups = $searchCriteria->getFilterGroups();

        $carrierFilters = [];
        foreach ($carrierCodes as $carrierCode) {
            $carrierFilters[]= $this->filterBuilder
                ->setField('main_table.shipping_method')
                ->setValue("$carrierCode%")
                ->setConditionType('like')
                ->create();
        }

        // Create another filter group for the carriers (OR group)
        $this->filterGroupBuilder->setFilters($carrierFilters);
        $carrierFilterGroup = $this->filterGroupBuilder->create();
        $filterGroups[]= $carrierFilterGroup;

        // Set all filter groups to the search criteria
        $searchCriteria->setFilterGroups($filterGroups);

        $searchResult = $this->orderRepository->getList($searchCriteria);
        if ($searchResult->getTotalCount() === 0) {
            $message = __(
                'No orders found for automatic label retrieval with status %1 in stores %2 for carriers %3.',
                $orderStatus,
                implode(', ', $storeIds),
                implode(', ', $carrierCodes)
            );
            throw new NoSuchEntityException($message);
        }

        return $searchResult;
    }
}
