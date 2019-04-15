<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class OrderServiceSelectionRepository
 * @package Dhl\ShippingCore\Model
 */
class OrderServiceSelectionRepository
{
    /**
     * @var ServiceSelection
     */
    private $resource;

    /**
     * @var ServiceSelectionCollectionFactory
     */
    private $collectionFactory;

    /**
     * QuoteServiceSelectionRepository constructor.
     *
     * @param ServiceSelection $resource
     * @param ServiceSelectionCollectionFactory $collectionFactory
     */
    public function __construct(
        ServiceSelection $resource,
        ServiceSelectionCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param OrderServiceSelection $serviceSelection
     * @throws CouldNotSaveException
     */
    public function save(OrderServiceSelection $serviceSelection)
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save: %1', $exception->getMessage()));
        }
    }

    /**
     * @param OrderServiceSelection $serviceSelection
     * @throws CouldNotDeleteException
     */
    public function delete(OrderServiceSelection $serviceSelection)
    {
        try {
            $this->resource->delete($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete ServiceSelection: %1', $exception->getMessage()));
        }
    }
}
