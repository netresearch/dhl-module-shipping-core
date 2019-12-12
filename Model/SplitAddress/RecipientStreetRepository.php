<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\SplitAddress;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Dhl\ShippingCore\Api\Data\RecipientStreetInterfaceFactory;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * RecipientStreetRepository
 *
 * @package Dhl\ShippingCore\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class RecipientStreetRepository implements RecipientStreetRepositoryInterface
{
    /**
     * @var RecipientStreetResource
     */
    private $resource;

    /**
     * @var RecipientStreetInterfaceFactory
     */
    private $recipientStreetFactory;

    /**
     * OrderAddressRepository constructor.
     *
     * @param RecipientStreetResource $resource
     * @param RecipientStreetInterfaceFactory $recipientStreetFactory
     */
    public function __construct(
        RecipientStreetResource $resource,
        RecipientStreetInterfaceFactory $recipientStreetFactory
    ) {
        $this->resource = $resource;
        $this->recipientStreetFactory = $recipientStreetFactory;
    }

    /**
     * Persist the recipient street object.
     *
     * @param RecipientStreetInterface $recipientStreet
     * @return RecipientStreetInterface
     * @throws CouldNotSaveException
     */
    public function save(RecipientStreetInterface $recipientStreet): RecipientStreetInterface
    {
        try {
            /** @var RecipientStreet $recipientStreet */
            $this->resource->save($recipientStreet);
        } catch (\Exception $exception) {
            if ($exception instanceof LocalizedException) {
                throw new CouldNotSaveException(__($exception->getMessage()));
            }

            throw new CouldNotSaveException(__('Unable to save recipient street.'), $exception);
        }

        return $recipientStreet;
    }

    /**
     * Get recipient street by order address id.
     *
     * @param int $orderAddressId
     * @return RecipientStreetInterface
     * @throws NoSuchEntityException
     */
    public function get(int $orderAddressId): RecipientStreetInterface
    {
        /** @var RecipientStreet $recipientStreet */
        $recipientStreet = $this->recipientStreetFactory->create();

        try {
            $this->resource->load($recipientStreet, $orderAddressId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Unable to load recipient street with ID "%1".', $orderAddressId));
        }

        if (!$recipientStreet->getId()) {
            throw new NoSuchEntityException(__('Recipient street with ID "%1" does not exist.', $orderAddressId));
        }

        return $recipientStreet;
    }
}
