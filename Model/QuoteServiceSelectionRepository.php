<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class QuoteServiceSelectionRepository
 * @package Dhl\ShippingCore\Model
 */
class QuoteServiceSelectionRepository
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
     */
    public function __construct(
        ServiceSelection $resource,
        ServiceSelectionCollectionFactory $collectionFactory
    ) {
        $this->resource = $resource;
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * @param QuoteServiceSelection $serviceSelection
     * @return QuoteServiceSelection
     * @throws CouldNotSaveException
     */
    public function save(QuoteServiceSelection $serviceSelection)
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save: %1', $exception->getMessage()));
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
            throw new CouldNotDeleteException(__('Could not delete ServiceSelection: %1', $exception->getMessage()));
        }
    }

    /**
     * @param string $addressId
     * @return ServiceSelectionCollection
     * @throws NoSuchEntityException
     */
    public function getByQuoteAddressId(string $addressId): ServiceSelectionCollection
    {
        $collection = $this->collectionFactory->create();
        $collection->addFilter('parent_id', $addressId);
        if ($collection->getSize() === 0) {
            throw new NoSuchEntityException(
                __('ServiceSelection for quote address id "%1" does not exist.', $addressId)
            );
        }

        return $collection;
    }

    /**
     * @param string $addressId
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteByQuoteAddressId(string $addressId): void
    {
        try {
            $items = $this->getByQuoteAddressId($addressId);
            foreach ($items as $item) {
                $this->delete($item);
            }
        } catch (NoSuchEntityException $e) {
            // fail silently
        }

    }
}
