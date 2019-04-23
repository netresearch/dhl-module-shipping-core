<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Webapi;

use Dhl\ShippingCore\Api\CheckoutManagementInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\ShippingOption\Selection\QuoteSelection;
use Dhl\ShippingCore\Model\ShippingOption\Selection\QuoteSelectionFactory;
use Dhl\ShippingCore\Model\ShippingOption\Selection\QuoteSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckoutManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutManagement implements CheckoutManagementInterface
{
    /**
     * @var CheckoutDataProvider
     */
    private $checkoutDataProvider;

    /**
     * @var CheckoutDataHydrator
     */
    private $checkoutDataHydrator;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var QuoteSelectionFactory
     */
    private $serviceSelectionFactory;

    /**
     * @var QuoteSelectionRepository
     */
    private $serviceSelectionRepository;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var SearchCriteriaBuilderFactory
     */
    private $searchCriteriaBuilderFactory;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * CheckoutDataManagement constructor.
     *
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param CheckoutDataHydrator $checkoutDataHydrator
     * @param StoreManagerInterface $storeManager
     * @param QuoteSelectionFactory $serviceSelectionFactory
     * @param QuoteSelectionRepository $serviceSelectionRepository
     * @param ShippingAddressManagementInterface $addressManagement
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator,
        StoreManagerInterface $storeManager,
        QuoteSelectionFactory $serviceSelectionFactory,
        QuoteSelectionRepository $serviceSelectionRepository,
        ShippingAddressManagementInterface $addressManagement,
        SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory,
        FilterBuilder $filterBuilder
    ) {
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->checkoutDataHydrator = $checkoutDataHydrator;
        $this->storeManager = $storeManager;
        $this->serviceSelectionFactory = $serviceSelectionFactory;
        $this->serviceSelectionRepository = $serviceSelectionRepository;
        $this->addressManagement = $addressManagement;
        $this->searchCriteriaBuilderFactory  = $searchCriteriaBuilderFactory;
        $this->filterBuilder = $filterBuilder;
    }

    /**
     * @param int $addressId
     * @throws CouldNotDeleteException
     */
    public function deleteServiceValues(int $addressId)
    {
        // clean up previously selected values
        $addressFilter = $this->filterBuilder
            ->setField(AssignedSelectionInterface::PARENT_ID)
            ->setValue($addressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        $previousSelection = $this->serviceSelectionRepository->getList($searchCriteria);

        /** @var QuoteSelection $selectedValue */
        foreach ($previousSelection as $selectedValue) {
            $this->serviceSelectionRepository->delete($selectedValue);
        }
    }

    /**
     * @param int $addressId
     * @param SelectionInterface[] $serviceSelection
     * @throws CouldNotSaveException
     */
    public function saveServiceValues(int $addressId, array $serviceSelection)
    {
        // persist new values
        foreach ($serviceSelection as $service) {
            /** @var SelectionInterface $service */
            $model = $this->serviceSelectionFactory->create();
            $model->setData([
                AssignedSelectionInterface::PARENT_ID => $addressId,
                AssignedSelectionInterface::SHIPPING_OPTION_CODE => $service->getShippingOptionCode(),
                AssignedSelectionInterface::INPUT_CODE => $service->getInputCode(),
                AssignedSelectionInterface::INPUT_VALUE => $service->getInputValue(),
            ]);
            $this->serviceSelectionRepository->save($model);
        }
    }

    /**
     * @param string $countryId
     * @param string $postalCode
     * @return CheckoutDataInterface
     * @throws NoSuchEntityException
     * @throws InputException
     */
    public function getCheckoutData(string $countryId, string $postalCode): CheckoutDataInterface
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $data = $this->checkoutDataProvider->getData($countryId, $storeId, $postalCode);

        return $this->checkoutDataHydrator->toObject($data);
    }

    /**
     * @param int $cartId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateShippingOptionSelections(int $cartId, array $shippingOptionSelections)
    {
        if (empty($shippingOptionSelections)) {
            return;
        }

        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->deleteServiceValues($shippingAddressId);
        $this->saveServiceValues($shippingAddressId, $shippingOptionSelections);
    }
}
