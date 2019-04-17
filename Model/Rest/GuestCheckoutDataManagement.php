<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Api\Rest\GuestCheckoutDataManagementInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Quote\Model\GuestCart\GuestShippingAddressManagementInterface;

/**
 * Class GuestCheckoutDataManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class GuestCheckoutDataManagement implements GuestCheckoutDataManagementInterface
{
    /**
     * @var GuestShippingAddressManagementInterface
     */
    private $addressManagement;

    /**
     * @var CheckoutDataManagementInterface|CheckoutDataManagement
     */
    private $serviceManagement;

    /**
     * GuestCartServiceManagement constructor.
     *
     * @param GuestShippingAddressManagementInterface $addressManagement
     * @param CheckoutDataManagementInterface $serviceManagement
     */
    public function __construct(
        GuestShippingAddressManagementInterface $addressManagement,
        CheckoutDataManagementInterface $serviceManagement
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
     * @param ServiceSelectionInterface[] $serviceSelection
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateServiceSelection(string $cartId, array $serviceSelection)
    {
        if (empty($serviceSelection)) {
            return;
        }

        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->serviceManagement->deleteServiceValues($shippingAddressId);
        $this->serviceManagement->saveServiceValues($shippingAddressId, $serviceSelection);
    }
}
