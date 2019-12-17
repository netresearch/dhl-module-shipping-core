<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionFactory;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelectionRepository;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelection;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class PersistShippingOptionSelectionObserver
 * @package Dhl\ShippingCore\Observer
 */
class PersistShippingOptionSelectionObserver implements ObserverInterface
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
     * @var QuoteSelectionRepository
     */
    private $quoteShippingOptionSelectionRepository;

    /**
     * @var OrderSelectionRepository
     */
    private $orderShippingOptionSelectionRepository;

    /**
     * @var OrderSelectionFactory
     */
    private $orderShippingOptionSelectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PersistShippingOptionSelectionObserver constructor.
     *
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     * @param QuoteSelectionRepository $quoteShippingOptionSelectionRepository
     * @param OrderSelectionRepository $orderShippingOptionSelectionReposotory
     * @param OrderSelectionFactory $orderShippingOptionSelectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder,
        QuoteSelectionRepository $quoteShippingOptionSelectionRepository,
        OrderSelectionRepository $orderShippingOptionSelectionReposotory,
        OrderSelectionFactory $orderShippingOptionSelectionFactory,
        LoggerInterface $logger
    ) {
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
        $this->quoteShippingOptionSelectionRepository = $quoteShippingOptionSelectionRepository;
        $this->orderShippingOptionSelectionRepository = $orderShippingOptionSelectionReposotory;
        $this->orderShippingOptionSelectionFactory = $orderShippingOptionSelectionFactory;
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
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($quote->getShippingAddress()->getId())
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();

        $serviceSelection = $this->quoteShippingOptionSelectionRepository->getList($searchCriteria);
        /** @var QuoteSelection $selection */
        foreach ($serviceSelection as $selection) {
            try {
                $model = $this->orderShippingOptionSelectionFactory->create();
                $model->setData([
                    AssignedSelectionInterface::PARENT_ID => $order->getShippingAddress()->getId(),
                    AssignedSelectionInterface::SHIPPING_OPTION_CODE => $selection->getShippingOptionCode(),
                    AssignedSelectionInterface::INPUT_CODE => $selection->getInputCode(),
                    AssignedSelectionInterface::INPUT_VALUE => $selection->getInputValue()
                ]);

                $this->orderShippingOptionSelectionRepository->save($model);
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
