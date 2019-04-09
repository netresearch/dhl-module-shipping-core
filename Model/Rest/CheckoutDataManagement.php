<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\QuoteServiceSelectionFactory;
use Dhl\ShippingCore\Model\QuoteServiceSelectionRepository;
use Magento\Quote\Model\QuoteRepository;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

/**
 * Class CheckoutDataManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class CheckoutDataManagement implements CheckoutDataManagementInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

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
    private $quoteServiceSelectionRepository;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $shippingAdressManagement;

    /**
     * CheckoutDataManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param CheckoutDataHydrator $checkoutDataHydrator
     * @param StoreManagerInterface $storeManager
     * @param QuoteServiceSelectionFactory $serviceSelectionFactory
     * @param QuoteServiceSelectionRepository $quoteSelectionRepository
     * @param ShippingAddressManagementInterface $shippingAddressManagement
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator,
        StoreManagerInterface $storeManager,
        QuoteServiceSelectionFactory $serviceSelectionFactory,
        QuoteServiceSelectionRepository $quoteSelectionRepository,
        ShippingAddressManagementInterface $shippingAddressManagement
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->checkoutDataHydrator = $checkoutDataHydrator;
        $this->storeManager = $storeManager;
        $this->serviceSelectionFactory = $serviceSelectionFactory;
        $this->quoteServiceSelectionRepository = $quoteSelectionRepository;
        $this->shippingAdressManagement = $shippingAddressManagement;
    }

    /**
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function getData(string $countryId, string $postalCode): CheckoutDataInterface
    {
        $storeId = (int)$this->storeManager->getStore()->getId();
        $data = $this->checkoutDataProvider->getData($countryId, $storeId, $postalCode);

        return $this->checkoutDataHydrator->toObject($data);
    }

    /**
     * Persist service selection with reference to a Quote Address ID.
     *
     * @param string $quoteId
     * @param array $serviceSelection
     *
     * @throws \Magento\Framework\Exception\CouldNotDeleteException
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setServiceSelection(string $quoteId, array $serviceSelection)
    {
        $addressId = (string)$this->shippingAdressManagement->get((int)$quoteId)->getId();
        $this->quoteServiceSelectionRepository->deleteByQuoteAddressId($addressId);

        foreach ($serviceSelection as $service) {
            $model = $this->serviceSelectionFactory->create();
            $model->setData(
                [
                    AssignedServiceSelectionInterface::PARENT_ID => $addressId,
                    AssignedServiceSelectionInterface::SERVICE_CODE => $service->getServiceCode(),
                    AssignedServiceSelectionInterface::INPUT_CODE => $service->getInputCode(),
                    AssignedServiceSelectionInterface::VALUE => $service->getValue()
                ]
            );
            $this->quoteServiceSelectionRepository->save($model);
        }
    }
}
