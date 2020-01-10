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

/**
 * SplitAddress Observer
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
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
     * SplitAddress constructor.
     *
     * @param RecipientStreetLoaderInterface $recipientStreetLoader
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param LoggerInterface $logger
     * @param string[] $carrierCodes
     */
    public function __construct(
        RecipientStreetLoaderInterface $recipientStreetLoader,
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        LoggerInterface $logger,
        array $carrierCodes = []
    ) {
        $this->recipientStreetLoader = $recipientStreetLoader;
        $this->recipientStreetRepository = $recipientStreetRepository;
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

        $recipientStreet = $this->recipientStreetLoader->load($address);

        try {
            $this->recipientStreetRepository->save($recipientStreet);
        } catch (CouldNotSaveException $exception) {
            $this->logger->error($exception->getLogMessage(), ['exception' => $exception]);
        }
    }
}
