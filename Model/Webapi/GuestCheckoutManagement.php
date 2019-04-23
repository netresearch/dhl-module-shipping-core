<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Webapi;

use Dhl\ShippingCore\Api\CheckoutManagementInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Api\GuestCheckoutManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;

/**
 * Class GuestCheckoutManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class GuestCheckoutManagement implements GuestCheckoutManagementInterface
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var CheckoutManagementInterface|CheckoutManagement
     */
    private $serviceManagement;

    /**
     * GuestCartServiceManagement constructor.
     *
     * @param GuestShippingAddressManagementInterface $addressManagement
     * @param CheckoutManagementInterface $serviceManagement
     */
    public function __construct(
        GuestShippingAddressManagementInterface $addressManagement,
        CheckoutManagementInterface $serviceManagement
    ) {
        $this->addressManagement = $addressManagement;
        $this->serviceManagement = $serviceManagement;
    }

    /**
     * Persist service selection.
     *
     * @fixme(nr): are webapi exceptions handled properly?
     *
     * @param string $cartId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateShippingOptionSelections(string $cartId, array $shippingOptionSelections)
    {
        if (empty($shippingOptionSelections)) {
            return;
        }

        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->serviceManagement->deleteServiceValues($shippingAddressId);
        $this->serviceManagement->saveServiceValues($shippingAddressId, $shippingOptionSelections);
    }
}
