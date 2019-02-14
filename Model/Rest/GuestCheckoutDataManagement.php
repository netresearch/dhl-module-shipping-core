<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rest;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
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
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getData(string $cartId, string $countryId, string $postalCode): CheckoutDataInterface
    {
        return $this->cartServiceManagement->getData($this->getQuoteId($cartId), $countryId, $postalCode);
    }

    /**
     * @param string $cartId
     * @param \Magento\Framework\Api\AttributeInterface[] $serviceSelection
     */
    public function setData(string $cartId, array $serviceSelection)
    {
        $this->cartServiceManagement->setData($this->getQuoteId($cartId), $serviceSelection);
    }

    /**
     * @param $cartId
     * @return int
     */
    private function getQuoteId($cartId): int
    {
        /** @var QuoteIdMask $quoteIdMask */
        $quoteIdMask = $this->quoteIdMaskFactory->create()->load($cartId, 'masked_id');

        return $quoteIdMask->getData('quote_id');
    }
}
