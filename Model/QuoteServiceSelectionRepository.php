<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollectionFactory;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class QuoteServiceSelectionRepository
 * @package Dhl\ShippingCore\Model
 */
class QuoteServiceSelectionRepository
{
    /**
     * @var ServiceSelectionCollectionFactory
     */
    private $collectionFactory;

    /**
     * @var CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * @var ServiceSelection
     */
    private $resource;

    /**
     * QuoteServiceSelectionRepository constructor.
     *
     * @param ServiceSelectionCollectionFactory $collectionFactory
     * @param CollectionProcessorInterface $collectionProcessor
     * @param ServiceSelection $resource
     */
    public function __construct(
        ServiceSelectionCollectionFactory $collectionFactory,
        CollectionProcessorInterface $collectionProcessor,
        ServiceSelection $resource
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->collectionProcessor = $collectionProcessor;
        $this->resource = $resource;
    }

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return ServiceSelectionCollection
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        $searchResult = $this->collectionFactory->create();
        $this->collectionProcessor->process($searchCriteria, $searchResult);

        return $searchResult;
    }

    /**
     * @param QuoteServiceSelection $serviceSelection
     * @return QuoteServiceSelection
     * @throws CouldNotSaveException
     */
    public function save(QuoteServiceSelection $serviceSelection): QuoteServiceSelection
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save service selection.'), $exception);
        }

        return $serviceSelection;
    }

    /**
     * @param QuoteServiceSelection $serviceSelection
     * @throws CouldNotDeleteException
     */
    public function delete(QuoteServiceSelection $serviceSelection)
    {
        try {
            $this->resource->delete($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete service selection.'), $exception);
        }
    }
}
