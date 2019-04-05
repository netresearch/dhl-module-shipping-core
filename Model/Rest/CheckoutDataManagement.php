<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Dhl\ShippingCore\Api\Data\Service\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataHydrator;
use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
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
     * CheckoutDataManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param CheckoutDataHydrator $checkoutDataHydrator
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        QuoteRepository $quoteRepository,
        CheckoutDataProvider $checkoutDataProvider,
        CheckoutDataHydrator $checkoutDataHydrator,
        StoreManagerInterface $storeManager
    ) {
        $this->quoteRepository = $quoteRepository;
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->checkoutDataHydrator = $checkoutDataHydrator;
        $this->storeManager = $storeManager;
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
     * @param ServiceSelectionInterface[] $serviceSelection
     */
    public function setServiceSelection(int $quoteId, array $serviceSelection)
    {
        // @TODO Persist service selection to DB (@see DHLGW-202)

        return;
    }
}
