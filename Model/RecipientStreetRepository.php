<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\RecipientStreetInterface;
use Dhl\ShippingCore\Api\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * RecipientStreetRepository
 *
 * @package Dhl\Paket\Model
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
     * @var RecipientStreetFactory
     */
    private $recipientStreetFactory;

    /**
     * OrderAddressRepository constructor.
     *
     * @param RecipientStreetResource $resource
     * @param RecipientStreetFactory $recipientStreetFactory
     */
    public function __construct(
        RecipientStreetResource $resource,
        RecipientStreetFactory $recipientStreetFactory
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

            throw new CouldNotSaveException(__('Unable to save dhl recipient street.'), $exception);
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
        $recipientStreet = $this->recipientStreetFactory->create();

        try {
            $this->resource->load($recipientStreet, $orderAddressId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Unable to load recipient street with id "%1".', $orderAddressId));
        }

        if (!$recipientStreet->getId()) {
            throw new NoSuchEntityException(__('Recipient street with id "%1" does not exist.', $orderAddressId));
        }

        return $recipientStreet;
    }
}
