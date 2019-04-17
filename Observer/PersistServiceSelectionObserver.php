<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Model\OrderServiceSelectionFactory;
use Dhl\ShippingCore\Model\OrderServiceSelectionRepository;
use Dhl\ShippingCore\Model\QuoteServiceSelection;
use Dhl\ShippingCore\Model\QuoteServiceSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class PersistServiceSelectionObserver
 * @package Dhl\ShippingCore\Observer
 */
class PersistServiceSelectionObserver implements ObserverInterface
{
    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PersistServiceSelectionObserver constructor.
     *
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param QuoteServiceSelectionRepository $quoteServiceSelectionRepository
     * @param OrderServiceSelectionRepository $orderServiceSelectionReposotory
     * @param OrderServiceSelectionFactory $orderServiceSelectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        QuoteServiceSelectionRepository $quoteServiceSelectionRepository,
        OrderServiceSelectionRepository $orderServiceSelectionReposotory,
        OrderServiceSelectionFactory $orderServiceSelectionFactory,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->quoteServiceSelectionRepository = $quoteServiceSelectionRepository;
        $this->orderServiceSelectionRepository = $orderServiceSelectionReposotory;
        $this->orderServiceSelectionFactory = $orderServiceSelectionFactory;
        $this->logger = $logger;
    }

    /**
     * Copy quote service selection to order address.
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        /** @var Order $order */
        $order = $observer->getData('order');
        /** @var Quote $quote */
        $quote = $observer->getData('quote');

        if ($order->getIsVirtual()) {
            return;
        }

        $addressFilter = $this->filterBuilder
            ->setField(AssignedServiceSelectionInterface::PARENT_ID)
            ->setValue($quote->getShippingAddress()->getId())
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();

        $serviceSelection = $this->quoteServiceSelectionRepository->getList($searchCriteria);
        /** @var QuoteServiceSelection $selection */
        foreach ($serviceSelection as $selection) {
            try {
                $model = $this->orderServiceSelectionFactory->create();
                $model->setData([
                    AssignedServiceSelectionInterface::PARENT_ID => $order->getShippingAddress()->getId(),
                    AssignedServiceSelectionInterface::SERVICE_CODE => $selection->getServiceCode(),
                    AssignedServiceSelectionInterface::INPUT_CODE => $selection->getInputCode(),
                    AssignedServiceSelectionInterface::INPUT_VALUE => $selection->getInputValue()
                ]);

                $this->orderServiceSelectionRepository->save($model);
            } catch (CouldNotSaveException $exception) {
                // observers do not throw exceptions, no exception must be thrown during order placement.
                $this->logger->error(
                    'An error occurred while copying selected services to the order',
                    ['exception' => $exception]
                );
            }
        }
    }
}
