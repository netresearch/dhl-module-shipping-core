<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetLoaderInterface;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Address;
use Psr\Log\LoggerInterface;

class SplitAddress implements ObserverInterface
{
    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var RecipientStreetLoaderInterface[]
     */
    private $streetLoaders;

    /**
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param LoggerInterface $logger
     * @param RecipientStreetLoaderInterface[] $streetLoaders
     */
    public function __construct(
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        LoggerInterface $logger,
        array $streetLoaders = []
    ) {
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->logger = $logger;
        $this->streetLoaders = $streetLoaders;
    }

    /**
     * Split address and save entity in custom table.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Address $address */
        $address = $observer->getData('address');
        if ($address->getAddressType() !== Address::TYPE_SHIPPING) {
            return;
        }

        $order = $address->getOrder();
        $carrierCode = strtok((string)$order->getShippingMethod(), '_');
        if (!isset($this->streetLoaders[$carrierCode])) {
            // carrier does not support split street
            return;
        }

        $streetLoader = $this->streetLoaders[$carrierCode];
        if (!$streetLoader instanceof RecipientStreetLoaderInterface) {
            throw new \RuntimeException("Type error: please register a street loader implementation for $carrierCode");
        }

        try {
            $recipientStreet = $streetLoader->load($address);
            $this->recipientStreetRepository->save($recipientStreet);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
