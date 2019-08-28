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
use Magento\Framework\Api\SearchCriteriaBuilder;
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
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * ApplySelectionsProcessor constructor.
     *
     * @param OrderSelectionRepository $selectionRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OrderSelectionRepository $selectionRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->selectionRepository = $selectionRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
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

        foreach ($selections as $selection) {
            foreach ($optionsData as $shippingOption) {
                if ($shippingOption->getCode() !== $selection->getShippingOptionCode()) {
                    continue;
                }

                foreach ($shippingOption->getInputs() as $input) {
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
        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter(
                AssignedSelectionInterface::PARENT_ID,
                $orderAddressId
            )->create();

        return $this->selectionRepository->getList($searchCriteria)->getItems();
    }
}
