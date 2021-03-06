<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelectionManager;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\ShippingAddressManagementInterface;
use Magento\Store\Model\StoreManagerInterface;

class CheckoutManagement implements CheckoutManagementInterface
{
    /**
     * @var CheckoutDataProvider
     */
    private $checkoutDataProvider;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var QuoteSelectionManager
     */
    private $selectionManager;

    /**
     * CheckoutManagement constructor.
     *
     * @param CheckoutDataProvider $checkoutDataProvider
     * @param StoreManagerInterface $storeManager
     * @param ShippingAddressManagementInterface $addressManagement
     * @param QuoteSelectionManager $selectionManager
     */
    public function __construct(
        CheckoutDataProvider $checkoutDataProvider,
        StoreManagerInterface $storeManager,
        ShippingAddressManagementInterface $addressManagement,
        QuoteSelectionManager $selectionManager
    ) {
        $this->checkoutDataProvider = $checkoutDataProvider;
        $this->storeManager = $storeManager;
        $this->addressManagement = $addressManagement;
        $this->selectionManager = $selectionManager;
    }

    /**
     * @param string $countryId
     * @param string $postalCode
     * @return ShippingDataInterface
     * @throws NoSuchEntityException
     * @throws LocalizedException
     */
    public function getCheckoutData(string $countryId, string $postalCode): ShippingDataInterface
    {
        $storeId = (int) $this->storeManager->getStore()->getId();

        return $this->checkoutDataProvider->getData($countryId, $storeId, $postalCode);
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
        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->selectionManager->updateSelections($shippingAddressId, $shippingOptionSelections);
    }
}
