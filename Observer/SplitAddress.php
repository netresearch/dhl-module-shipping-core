<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetLoaderInterface;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\SplitAddress\RecipientStreet;
use Dhl\ShippingCore\Model\Util\StreetSplitter;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order\Address;
use Psr\Log\LoggerInterface;

class SplitAddress implements ObserverInterface
{
    /**
     * @var RecipientStreetLoaderInterface
     */
    private $recipientStreetLoader;

    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string[]
     */
    private $carrierCodes;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    /**
     * SplitAddress constructor.
     *
     * @param RecipientStreetLoaderInterface $recipientStreetLoader
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param StreetSplitter $streetSplitter
     * @param LoggerInterface $logger
     * @param string[] $carrierCodes
     */
    public function __construct(
        RecipientStreetLoaderInterface $recipientStreetLoader,
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        StreetSplitter $streetSplitter,
        LoggerInterface $logger,
        array $carrierCodes = []
    ) {
        $this->recipientStreetLoader = $recipientStreetLoader;
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->streetSplitter = $streetSplitter;
        $this->logger = $logger;
        $this->carrierCodes = $carrierCodes;
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
        $shippingMethod = strtok((string)$order->getShippingMethod(), '_');
        if (!in_array($shippingMethod, $this->carrierCodes, true)) {
            // carrier does not support split street
            return;
        }

        /** @var RecipientStreet $recipientStreet */
        $recipientStreet = $this->recipientStreetLoader->load($address);
        $street = implode(', ', $address->getStreet());
        $addressParts = $this->streetSplitter->splitStreet($street);

        $recipientStreet->addData([
            RecipientStreetInterface::NAME => $addressParts['street_name'],
            RecipientStreetInterface::NUMBER => $addressParts['street_number'],
            RecipientStreetInterface::SUPPLEMENT => $addressParts['supplement'],
        ]);

        try {
            $this->recipientStreetRepository->save($recipientStreet);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
