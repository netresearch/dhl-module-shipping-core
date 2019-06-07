<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelectionCollection;
use Dhl\ShippingCore\Model\ShippingOption\Selection\OrderSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class ApplySelectionsProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ApplySelectionsProcessor extends AbstractProcessor
{
    /**
     * @var OrderSelectionRepository
     */
    private $selectionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * ApplySelectionsProcessor constructor.
     *
     * @param OrderSelectionRepository $selectionRepository
     * @param FilterBuilder $filterBuilder
     * @param LoggerInterface $logger
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        OrderSelectionRepository $selectionRepository,
        FilterBuilder $filterBuilder,
        LoggerInterface $logger,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->selectionRepository = $selectionRepository;
        $this->filterBuilder = $filterBuilder;
        $this->logger = $logger;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    public function processShippingOptions(array $optionsData, Order\Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_SERVICE) {
            return $optionsData;
        }

        $orderAddressId = $shipment->getShippingAddressId();
        $selections = $this->loadSelections($orderAddressId);

        foreach ($selections->getItems() as $selection) {
            /** @var AssignedSelectionInterface $selection */
            $option = $selection->getShippingOptionCode();
            $input = $selection->getInputCode();
            if (isset($optionsData[$option]['inputs'][$input])) {
                $optionsData[$option]['inputs'][$input]['defaultValue'] = $selection->getInputValue();
            } else {
                $message = "Selection for shipping option $option.$input was not committed to packaging "
                    . 'because the option is not available at packaging.';
                $this->logger->warning($message);
            }
        }

        return $optionsData;
    }

    /**
     * @param int $orderAddressId
     * @return ShippingOptionSelectionCollection
     */
    private function loadSelections(int $orderAddressId): ShippingOptionSelectionCollection
    {
        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($orderAddressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();

        return $this->selectionRepository->getList($searchCriteria);
    }
}
