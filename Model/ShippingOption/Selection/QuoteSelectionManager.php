<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Selection;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class SelectionManager
 *
 * @package Dhl\ShippingCore\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class QuoteSelectionManager
{
    /**
     * @var QuoteSelectionFactory
     */
    private $quoteSelectionFactory;

    /**
     * @var QuoteSelectionRepository
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
     * SelectionManager constructor.
     *
     * @param QuoteSelectionFactory $quoteSelectionFactory
     * @param QuoteSelectionRepository $selectionRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     */
    public function __construct(
        QuoteSelectionFactory $quoteSelectionFactory,
        QuoteSelectionRepository $selectionRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
    ) {
        $this->quoteSelectionFactory = $quoteSelectionFactory;
        $this->selectionRepository = $selectionRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilderFactory = $searchCriteriaBuilderFactory;
    }

    /**
     * Update the given shipping option selections in persistent storage.
     *
     * @param int $shippingAddressId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     */
    public function updateSelections(int $shippingAddressId, array $shippingOptionSelections)
    {
        $this->deleteSelections($shippingAddressId);
        $this->saveSelections($shippingAddressId, $shippingOptionSelections);
    }

    /**
     * Delete all Selections for the given Quote address id.
     *
     * @param int $addressId
     * @throws CouldNotDeleteException
     */
    private function deleteSelections(int $addressId)
    {
        // clean up previously selected values
        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($addressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        $previousSelections = $this->selectionRepository->getList($searchCriteria);

        /** @var QuoteSelection $selection */
        foreach ($previousSelections as $selection) {
            $this->selectionRepository->delete($selection);
        }
    }

    /**
     * Store the given Selection
     *
     * @param int $addressId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotSaveException
     */
    private function saveSelections(int $addressId, array $shippingOptionSelections)
    {
        foreach ($shippingOptionSelections as $selection) {
            /** @var SelectionInterface $selection */
            $model = $this->quoteSelectionFactory->create();
            $model->setData(
                [
                    AssignedSelectionInterface::PARENT_ID => $addressId,
                    AssignedSelectionInterface::SHIPPING_OPTION_CODE => $selection->getShippingOptionCode(),
                    AssignedSelectionInterface::INPUT_CODE => $selection->getInputCode(),
                    AssignedSelectionInterface::INPUT_VALUE => $selection->getInputValue(),
                ]
            );
            $this->selectionRepository->save($model);
        }
    }
}
