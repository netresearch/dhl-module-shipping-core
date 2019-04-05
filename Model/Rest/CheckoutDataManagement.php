<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\QuoteServiceSelection;
use Dhl\ShippingCore\Model\QuoteServiceSelectionFactory;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\ServiceSelectionRepository;
use Dhl\ShippingCore\Setup\Setup;
use Magento\Config\Model\Config\Source\YesnoFactory;
use Magento\Quote\Model\QuoteRepository;
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
     * @var ServiceSelectionRepository
     */
    private $serviceSelectionRepository;

    /**
     * CheckoutDataManagement constructor.
     * @param QuoteRepository $quoteRepository
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param CheckoutDataHydrator $checkoutDataHydrator
     * @param StoreManagerInterface $storeManager
     * @param QuoteServiceSelectionFactory $serviceSelectionFactory
     * @param ServiceSelectionRepository $serviceSelectionRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator,
        StoreManagerInterface $storeManager,
        QuoteServiceSelectionFactory $serviceSelectionFactory,
        ServiceSelectionRepository $serviceSelectionRepository
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->checkoutDataHydrator = $checkoutDataHydrator;
        $this->storeManager = $storeManager;
        $this->serviceSelectionFactory = $serviceSelectionFactory;
        $this->serviceSelectionRepository = $serviceSelectionRepository;
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
     * @param int $quoteId
     * @param array $serviceSelection
     * @throws \Magento\Framework\Exception\CouldNotSaveException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function setServiceSelection(int $quoteId, array $serviceSelection)
    {
        $quote = $this->quoteRepository->get($quoteId);
        $addressId = $quote->getShippingAddress()->getId();

        foreach ($serviceSelection as $service) {
            $model = $this->serviceSelectionFactory->create();
            $model->setData(
                [
                    Setup::SERVICE_SELECTION_PARENT_ID => $addressId,
                    Setup::SERVICE_SELECTION_SERVICE_CODE => $service->getServiceCode(),
                    Setup::SERVICE_SELECTION_INPUT_CODE => $service->getInputCode(),
                    Setup::SERVICE_SELECTION_VALUE => $service->getValue()
                ]
            );
            $this->serviceSelectionRepository->save($model);
        }
    }
}
