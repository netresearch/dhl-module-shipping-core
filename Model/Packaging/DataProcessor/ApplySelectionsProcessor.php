<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Dhl\ShippingCore\Model\ShippingOption\Selection\OrderSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Sales\Model\Order\Shipment;

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
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * ApplySelectionsProcessor constructor.
     *
     * @param OrderSelectionRepository $selectionRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        OrderSelectionRepository $selectionRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->selectionRepository = $selectionRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * Filters all not selected customer services out of the options data array.
     *
     * @param AssignedSelectionInterface[] $selections
     * @param array $optionsData
     * @return array
     */
    private function filterNotSelectedServices(array $selections, array $optionsData): array
    {
        $availableCustomerServices = [
            'preferredDay',
            'preferredTime',
            'preferredLocation',
            'preferredNeighbour',
            "parcelstation",
        ];

        $selectedServices = [];
        foreach ($selections as $selection) {
            $selectedServices[] = $selection->getShippingOptionCode();
        }

        $notSelectedServices = array_diff($availableCustomerServices, array_unique($selectedServices));

        foreach ($optionsData as $optionCode => $shippingOption) {
            if (in_array($shippingOption->getCode(), $notSelectedServices, true)) {
                unset($optionsData[$optionCode]);
            }
        }

        return $optionsData;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_SERVICE) {
            return $optionsData;
        }

        $addressId = (int) $shipment->getShippingAddressId();
        $selections = $this->loadSelections($addressId);
        $optionsData = $this->filterNotSelectedServices($selections, $optionsData);

        foreach ($selections as $selection) {
            foreach ($optionsData as $shippingOption) {
                if ($shippingOption->getCode() !== $selection->getShippingOptionCode()) {
                    continue;
                }

                foreach ($shippingOption->getInputs() as $input) {
                    if ($input->getCode() === 'enabled') {
                        $input->setDefaultValue('1');
                    }

                    if ($input->getCode() !== $selection->getInputCode()) {
                        continue;
                    }

                    $input->setDefaultValue($selection->getInputValue());
                }
            }
        }

        return $optionsData;
    }

    /**
     * @param int $orderAddressId
     * @return AssignedSelectionInterface[]
     */
    private function loadSelections(int $orderAddressId): array
    {
        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($orderAddressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();

        return $this->selectionRepository->getList($searchCriteria)->getItems();
    }
}
