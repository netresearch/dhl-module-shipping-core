<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Model\OrderServiceSelectionFactory;
use Dhl\ShippingCore\Model\OrderServiceSelectionRepository;
use Dhl\ShippingCore\Model\QuoteServiceSelectionRepository;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

/**
 * Class PersistServiceSelectionObserver
 * @package Dhl\ShippingCore\Observer
 */
class PersistServiceSelectionObserver implements ObserverInterface
{
    /**
     * @var QuoteServiceSelectionRepository
     */
    private $quoteServiceSelectionRepository;

    /**
     * @var OrderServiceSelectionRepository
     */
    private $orderServiceSelectionRepository;

    /**
     * @var OrderServiceSelectionFactory
     */
    private $orderServiceSelectionFactory;

    /**
     * PersistServiceSelectionObserver constructor.
     * @param QuoteServiceSelectionRepository $quoteServiceSelectionRepository
     * @param OrderServiceSelectionRepository $orderServiceSelectionReposotory
     * @param OrderServiceSelectionFactory $orderServiceSelectionFactory
     */
    public function __construct(
        QuoteServiceSelectionRepository $quoteServiceSelectionRepository,
        OrderServiceSelectionRepository $orderServiceSelectionReposotory,
        OrderServiceSelectionFactory $orderServiceSelectionFactory
    ) {
        $this->quoteServiceSelectionRepository = $quoteServiceSelectionRepository;
        $this->orderServiceSelectionRepository = $orderServiceSelectionReposotory;
        $this->orderServiceSelectionFactory = $orderServiceSelectionFactory;
    }

    /**
     * Persist service selection with reference to an Order Address ID.
     *
     * @param Observer $observer
     * @return $this|void
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getDataByKey('order');
        /** @var Quote $quote */
        $quote = $observer->getDataByKey('quote');

        if ($order->getIsVirtual()) {
            return;
        }

        $quoteAddressId = (string)$quote->getShippingAddress()->getId();
        try {
            $serviceSelection = $this->quoteServiceSelectionRepository->getByQuoteAddressId($quoteAddressId);
        } catch (\Exception $exception) {
            return;
        }

        foreach ($serviceSelection as $selection) {
            $model = $this->orderServiceSelectionFactory->create();
            $model->setData(
                [
                    AssignedServiceSelectionInterface::PARENT_ID => $order->getShippingAddress()->getId(),
                    AssignedServiceSelectionInterface::SERVICE_CODE => $selection->getServiceCode(),
                    AssignedServiceSelectionInterface::INPUT_CODE => $selection->getInputCode(),
                    AssignedServiceSelectionInterface::VALUE => $selection->getValue()
                ]
            );
            $this->orderServiceSelectionRepository->save($model);
        }
    }
}
