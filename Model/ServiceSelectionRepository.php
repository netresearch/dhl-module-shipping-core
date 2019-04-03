<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\ServiceSelectionRepositoryInterface;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelection as OrderServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollection as OrderServiceSeletionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollectionFactory
    as OrderServiceSeletionCollectionFactory;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection as QuoteServiceSelection;
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
     * @var QuoteServiceSelectionCollectionFactory
     */
    private $quoteCollectionFactory;

    /**
     * @var QuoteServiceSelection
     */
    private $quoteRessource;

    /**
     * @var OrderServiceSeletionCollectionFactory
     */
    private $orderCollectionFactory;

    /**
     * @var OrderServiceSelection
     */
    private $orderResource;

    /**
     * ServiceSelectionRepository constructor.
     *
     * @param QuoteServiceSelectionCollectionFactory $quoteCollectionFactory
     * @param QuoteServiceSelection $quoteRessource
     * @param OrderServiceSeletionCollectionFactory $orderCollectionFactory
     * @param OrderServiceSelection $orderResource
     */
    public function __construct(
        QuoteServiceSelectionCollectionFactory $quoteCollectionFactory,
        QuoteServiceSelection $quoteRessource,
        OrderServiceSeletionCollectionFactory $orderCollectionFactory,
        OrderServiceSelection $orderResource
    ) {
        $this->quoteCollectionFactory = $quoteCollectionFactory;
        $this->quoteRessource = $quoteRessource;
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->orderResource = $orderResource;
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
