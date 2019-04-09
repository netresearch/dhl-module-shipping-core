<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;
use Dhl\ShippingCore\Api\Rest\CheckoutDataManagementInterface;
use Dhl\ShippingCore\Api\Rest\GuestCheckoutDataManagementInterface;
use Magento\Quote\Model\QuoteIdMask;
use Magento\Quote\Model\QuoteIdMaskFactory;

/**
 * Class GuestCheckoutDataManagement
 *
 * @package Dhl\ShippingCore\Model\Rest
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class GuestCheckoutDataManagement implements GuestCheckoutDataManagementInterface
{
    /**
     * @var QuoteIdMaskFactory
     */
    private $quoteIdMaskFactory;

    /**
     * @var CheckoutDataManagementInterface
     */
    private $cartServiceManagement;

    /**
     * GuestCartServiceManagement constructor.
     *
     * @param QuoteIdMaskFactory $quoteIdMaskFactory
     * @param CheckoutDataManagementInterface $cartServiceMngmt
     */
    public function __construct(
        QuoteIdMaskFactory $quoteIdMaskFactory,
        CheckoutDataManagementInterface $cartServiceMngmt
    ) {
        $this->quoteIdMaskFactory = $quoteIdMaskFactory;
        $this->cartServiceManagement = $cartServiceMngmt;
    }

    /**
     * @param string $cartId
     * @param ServiceSelectionInterface[] $serviceSelection
     */
    public function setServiceSelection(string $cartId, array $serviceSelection)
    {
        $this->cartServiceManagement->setServiceSelection($this->getQuoteId($cartId), $serviceSelection);
    }

    /**
     * @param $cartId
     * @return int
     */
    private function getQuoteId($cartId): string
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $quoteIdMask->getData('quote_id');
    }
}
