<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\SplitAddress;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * RecipientStreetRepositoryInterface
 *
 * @api
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
interface RecipientStreetRepositoryInterface
{
    /**
     * Save recipient street object.
     *
     * @param RecipientStreetInterface $recipientStreet
     * @return RecipientStreetInterface
     * @throws CouldNotSaveException
     */
    public function save(RecipientStreetInterface $recipientStreet): RecipientStreetInterface;

    /**
     * Get recipient street by primary key.
     *
     * @param int $orderAddressId
     * @return RecipientStreetInterface
     * @throws NoSuchEntityException
     */
    public function get(int $orderAddressId): RecipientStreetInterface;
}
