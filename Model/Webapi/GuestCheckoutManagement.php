<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Webapi;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Api\GuestCheckoutManagementInterface;
use Dhl\ShippingCore\Model\ShippingOption\Selection\QuoteSelectionManager;
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
     * @var QuoteSelectionManager
     */
    private $selectionManager;

    /**
     * GuestCartServiceManagement constructor.
     *
     * @param GuestShippingAddressManagementInterface $addressManagement
     * @param QuoteSelectionManager $selectionManager
     */
    public function __construct(
        GuestShippingAddressManagementInterface $addressManagement,
        QuoteSelectionManager $selectionManager
    ) {
        $this->addressManagement = $addressManagement;
        $this->selectionManager = $selectionManager;
    }

    /**
     * Persist service selection.
     *
     * @param string $cartId
     * @param SelectionInterface[] $shippingOptionSelections
     * @throws CouldNotDeleteException
     * @throws CouldNotSaveException
     * @throws NoSuchEntityException
     */
    public function updateShippingOptionSelections(string $cartId, array $shippingOptionSelections)
    {
        $shippingAddressId = (int) $this->addressManagement->get($cartId)->getId();
        $this->selectionManager->updateSelections($shippingAddressId, $shippingOptionSelections);
    }
}
