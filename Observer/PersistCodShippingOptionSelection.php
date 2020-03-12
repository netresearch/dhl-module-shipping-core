<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\CodSelectorInterface;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionFactory;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * PersistCodShippingOptionSelection Observer.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link   https://www.netresearch.de/
 */
class PersistCodShippingOptionSelection implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OrderSelectionRepository
     */
    private $orderSelectionRepository;

    /**
     * @var OrderSelectionFactory
     */
    private $optionSelectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var CodSelectorInterface[]
     */
    private $codSelectors;

    /**
     * PersistCodShippingOptionSelection constructor.
     *
     * @param ConfigInterface $config
     * @param OrderSelectionRepository $orderSelectionRepository
     * @param OrderSelectionFactory $optionSelectionFactory
     * @param LoggerInterface $logger
     * @param CodSelectorInterface[] $codSelectors
     */
    public function __construct(
        ConfigInterface $config,
        OrderSelectionRepository $orderSelectionRepository,
        OrderSelectionFactory $optionSelectionFactory,
        LoggerInterface $logger,
        array $codSelectors = []
    ) {
        $this->config = $config;
        $this->orderSelectionRepository = $orderSelectionRepository;
        $this->optionSelectionFactory = $optionSelectionFactory;
        $this->logger = $logger;
        $this->codSelectors = $codSelectors;
    }

    /**
     * If CoD payment was chosen during checkout, persist the service selection.
     *
     * Selection data population (option code, input code, input value) is delegated to the carrier.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');
        if (!$order || !$order->getPayment() || $order->getIsVirtual()) {
            return;
        }

        $isCodPayment = $this->config->isCodPaymentMethod($order->getPayment()->getMethod(), $order->getStoreId());
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');

        if (!$isCodPayment || !isset($this->codSelectors[$carrierCode])) {
            return;
        }

        $selection = $this->optionSelectionFactory->create();
        $selection->setData([AssignedSelectionInterface::PARENT_ID => $order->getShippingAddress()->getId()]);
        $this->codSelectors[$carrierCode]->assignCodSelection($selection);

        try {
            $this->orderSelectionRepository->save($selection);
        } catch (CouldNotSaveException $exception) {
            // observers do not throw exceptions, no exception must be thrown during order placement.
            $message = sprintf('Could not save Cash on Delivery service for order %s.', $order->getEntityId());
            $this->logger->error($message, ['exception' => $exception]);
        }
    }
}
