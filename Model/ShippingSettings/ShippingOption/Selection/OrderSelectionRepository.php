<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelectionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class OrderServiceSelectionRepository
 *
 * @package Dhl\ShippingCore\Model
 */
class OrderSelectionRepository
{
    /**
     * @var ShippingOptionSelectionCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ShippingOptionSelection
     */
    private $resource;

    /**
     * OrderSelectionRepository constructor.
     *
     * @param ShippingOptionSelectionCollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ShippingOptionSelection $resource
     */
    public function __construct(
        ShippingOptionSelectionCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ShippingOptionSelection $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->resource = $resource;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ShippingOptionSelectionCollection
     */
    public function getList(SearchCriteriaInterface $searchCriteria): ShippingOptionSelectionCollection
    {
        $searchResult = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);

        return $searchResult;
    }

    /**
     * @param OrderSelection $serviceSelection
     * @return OrderSelection
     * @throws CouldNotSaveException
     */
    public function save(OrderSelection $serviceSelection): OrderSelection
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save service selection.'), $exception);
        }

        return $serviceSelection;
    }

    /**
     * @param OrderSelection $serviceSelection
     * @throws CouldNotDeleteException
     */
    public function delete(OrderSelection $serviceSelection)
    {
        try {
            $this->resource->delete($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete service selection.'), $exception);
        }
    }
}
