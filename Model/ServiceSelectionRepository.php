<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\ServiceSelectionRepositoryInterface;
use Dhl\ShippingCore\Model\OrderServiceSelection;
use Dhl\ShippingCore\Model\QuoteServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelection as OrderResource;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollection as OrderServiceSeletionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollectionFactory
    as OrderServiceSelectionCollectionFactory;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection as QuoteResource;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollection as QuoteServiceSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollectionFactory
    as QuoteServiceSelectionCollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ServiceSelectionRepository
 * @package Dhl\ShippingCore\Model
 */
class ServiceSelectionRepository implements ServiceSelectionRepositoryInterface
{
    /**
     * @var OrderResource
     */
    private $orderResource;
    /**
     * @var OrderServiceSelectionCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var QuoteResource
     */
    private $quoteRessource;

    /**
     * @var QuoteServiceSelectionCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * ServiceSelectionRepository constructor.
     *
     * @param OrderResource $orderResource
     * @param OrderServiceSelectionCollectionFactory $orderCollectionFactory
     * @param QuoteResource $quoteResource
     * @param QuoteServiceSelectionCollectionFactory $quoteCollectionFactory

     */
    public function __construct(
        OrderResource $orderResource,
        OrderServiceSelectionCollectionFactory $orderCollectionFactory,
        QuoteResource $quoteResource,
        QuoteServiceSelectionCollectionFactory $quoteCollectionFactory
    ) {
        $this->orderResource = $orderResource;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->quoteRessource = $quoteResource;
        $this->quoteCollectionFactory = $quoteCollectionFactory;
    }

    /**
     * @param ServiceSelectionInterface|AbstractModel $serviceSelection
     *
     * @return ServiceSelectionInterface
     * @throws CouldNotSaveException
     */
    public function save(ServiceSelectionInterface $serviceSelection): ServiceSelectionInterface
    {
        try {
            if (get_class($serviceSelection) === OrderServiceSelection::class) {
                $this->orderResource->save($serviceSelection);
            } else {
                $this->quoteRessource->save($serviceSelection);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save: %1', $exception->getMessage()));
        }

        return $serviceSelection;
    }

    /**
     * @param ServiceSelectionInterface|AbstractModel $serviceSelection
     *
     * @return bool
     * @throws CouldNotSaveException
     */
    public function delete(ServiceSelectionInterface $serviceSelection): bool
    {
        try {
            if (get_class($serviceSelection) === OrderServiceSelection::class) {
                $this->orderResource->delete($serviceSelection);
            } else {
                $this->quoteRessource->delete($serviceSelection);
            }
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not delete ServiceSelection: %1', $exception->getMessage()));
        }
    }

    /**
     * @param string $addressId
     *
     * @return QuoteServiceSelectionCollection
     * @throws NoSuchEntityException
     */
    public function getByQuoteAddressId(string $addressId): QuoteServiceSelectionCollection
    {
        $collection = $this->quoteCollectionFactory->create();
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
     *
     * @return OrderServiceSeletionCollection
     * @throws NoSuchEntityException
     */
    public function getByOrderAddressId(string $addressId): OrderServiceSeletionCollection
    {
        $collection = $this->orderCollectionFactory->create();
        $collection->addFilter('parent_id', $addressId);
        if ($collection->getSize() === 0) {
            throw new NoSuchEntityException(
                __('ServiceSelection for order address id "%1" does not exist.', $addressId)
            );
        }

        return $collection;
    }

    /**
     * @param string $addressId
     *
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function deleteByOrderAddressId(string $addressId): void
    {
        $items = $this->getByOrderAddressId($addressId);
        foreach ($items as $item) {
            $this->delete($item);
        }
    }

    /**
     * @param string $addressId
     *
     * @throws CouldNotSaveException
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
