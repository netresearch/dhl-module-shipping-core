<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\QuoteServiceSelection;
use Dhl\ShippingCore\Model\QuoteServiceSelectionFactory;
use Dhl\ShippingCore\Model\QuoteServiceSelectionRepository;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\SearchCriteriaBuilderFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckoutDataManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutDataManagement implements CheckoutDataManagementInterface
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
     * @var QuoteServiceSelectionFactory
     */
    private $serviceSelectionFactory;

    /**
     * @var QuoteServiceSelectionRepository
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
     * @param QuoteServiceSelectionFactory $serviceSelectionFactory
     * @param QuoteServiceSelectionRepository $serviceSelectionRepository
     * @param ShippingAddressManagementInterface $addressManagement
     * @param SearchCriteriaBuilderFactory $searchCriteriaBuilderFactory
     * @param FilterBuilder $filterBuilder
     */
    public function __construct(
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator,
        StoreManagerInterface $storeManager,
        QuoteServiceSelectionFactory $serviceSelectionFactory,
        QuoteServiceSelectionRepository $serviceSelectionRepository,
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
            ->setField(AssignedServiceSelectionInterface::PARENT_ID)
            ->setValue($addressId)
            ->setConditionType('eq')
            ->create();

        $searchCriteriaBuilder = $this->searchCriteriaBuilderFactory->create();
        $searchCriteria = $searchCriteriaBuilder->addFilter($addressFilter)->create();
        $previousSelection = $this->serviceSelectionRepository->getList($searchCriteria);

        /** @var QuoteServiceSelection $selectedValue */
        foreach ($previousSelection as $selectedValue) {
            $this->serviceSelectionRepository->delete($selectedValue);
        }
    }

    /**
     * @param int $addressId
     * @param ServiceSelectionInterface[] $serviceSelection
     * @throws CouldNotSaveException
     */
    public function saveServiceValues(int $addressId, array $serviceSelection)
    {
        // persist new values
        foreach ($serviceSelection as $service) {
            $model = $this->serviceSelectionFactory->create();
            $model->setData([
                AssignedServiceSelectionInterface::PARENT_ID => $addressId,
                AssignedServiceSelectionInterface::SERVICE_CODE => $service->getServiceCode(),
                AssignedServiceSelectionInterface::INPUT_CODE => $service->getInputCode(),
                AssignedServiceSelectionInterface::INPUT_VALUE => $service->getInputValue(),
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
    public function getData(string $countryId, string $postalCode): CheckoutDataInterface
    {
        $storeId = (int) $this->storeManager->getStore()->getId();
        $data = $this->checkoutDataProvider->getData($countryId, $storeId, $postalCode);

        return $this->checkoutDataHydrator->toObject($data);
    }

    /**
     * Persist service selection.
     *
     * @fixme(nr): are webapi exceptions handled properly?
     *
     * @param int $cartId
     * @param ServiceSelectionInterface[] $serviceSelection
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateServiceSelection(int $cartId, array $serviceSelection)
    {
        if (empty($serviceSelection)) {
            return;
        }

        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->deleteServiceValues($shippingAddressId);
        $this->saveServiceValues($shippingAddressId, $serviceSelection);
    }
}
