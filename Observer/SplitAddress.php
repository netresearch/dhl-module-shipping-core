<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\RecipientStreetInterface;
use Dhl\ShippingCore\Api\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\RecipientStreetFactory;
use Dhl\ShippingCore\Util\StreetSplitter;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Address;

/**
 * SplitAddress Observer
 *
 * @package Dhl\ShippingCore\Observer
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class SplitAddress implements ObserverInterface
{
    /**
     * @var RecipientStreetFactory
     */
    private $recipientStreetFactory;

    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    /**
     * @var string[]
     */
    private $carrierCodes;

    /**
     * SplitAddress constructor.
     * @param RecipientStreetFactory $recipientStreetFactory
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param StreetSplitter $streetSplitter
     * @param string[] $carrierCodes
     */
    public function __construct(
        RecipientStreetFactory $recipientStreetFactory,
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        StreetSplitter $streetSplitter,
        array $carrierCodes = []
    ) {
        $this->recipientStreetFactory = $recipientStreetFactory;
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->streetSplitter = $streetSplitter;
        $this->carrierCodes = $carrierCodes;
    }

    /**
     * Split address and save entity in custom table.
     *
     * @param Observer $observer
     * @throws CouldNotSaveException
     */
    public function execute(Observer $observer)
    {
        /** @var Address $address */
        $address = $observer->getData('address');
        if ($address->getAddressType() !== Address::TYPE_SHIPPING) {
            return;
        }

        $order = $address->getOrder();
        $shippingMethod = strtok((string)$order->getShippingMethod(), '_');
        if (!in_array($shippingMethod, $this->carrierCodes, true)) {
            // carrier does not support split street
            return;
        }

        $street = implode(' ', $address->getStreet());
        $addressParts = $this->streetSplitter->splitStreet($street);

        $orderAddress = $this->recipientStreetFactory->create();
        $orderAddress->setData([
            RecipientStreetInterface::ORDER_ADDRESS_ID => $address->getEntityId(),
            RecipientStreetInterface::NAME => $addressParts['street_name'],
            RecipientStreetInterface::NUMBER => $addressParts['street_number'],
            RecipientStreetInterface::SUPPLEMENT => $addressParts['supplement'],
        ]);

        $this->recipientStreetRepository->save($orderAddress);
    }
}
