<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelectionCollection as QuoteServiceSelectionCollection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelectionCollection as OrderServiceSelectionCollection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Interface ServiceSelectionRepositoryInterface
 * @package Dhl\ShippingCore\Api
 */
interface ServiceSelectionRepositoryInterface
{
    /**
     * @param ServiceSelectionInterface $serviceSelection
     *
     * @return ServiceSelectionInterface
     * @throws CouldNotSaveException
     */
    public function save(ServiceSelectionInterface $serviceSelection): ServiceSelectionInterface;

    /**
     * @param string $addressId
     *
     * @return QuoteServiceSelectionCollection
     * @throws NoSuchEntityException
     */
    public function getByQuoteAddressId(string $addressId): QuoteServiceSelectionCollection;

    /**
     * @param string $addressId
     *
     * @return OrderServiceSelectionCollection
     * @throws NoSuchEntityException
     */
    public function getByOrderAddressId(string $addressId): OrderServiceSelectionCollection;

    /**
     * @param ServiceSelectionInterface $serviceSelection
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(ServiceSelectionInterface $serviceSelection): bool;

    /**
     * @param string $addressId
     *
     * @return void
     * @throws CouldNotDeleteException
     */
    public function deleteByQuoteAddressId(string $addressId): void;

    /**
     * @param string $addressId
     *
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */
    public function deleteByOrderAddressId(string $addressId): void;
}
