<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Api\TaxConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;

/**
 * Class TaxConfig
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class TaxConfig implements TaxConfigInterface
{

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * TaxConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getShippingTaxClass($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SHIPPING_TAX_CLASS,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    public function isShippingPriceInclTax($scopeId = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_XML_PATH_SHIPPING_INCLUDES_TAX,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    public function displayCartPriceIncludingTax($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displayCartPriceExcludingTax($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displayCartBothPrices($scopeId = null): bool
    {
        return $this->getCartPriceDisplayType($scopeId) === self::DISPLAY_TYPE_BOTH;
    }

    public function getCartPriceDisplayType($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_CART_SHIPPING,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    public function displaySalesPriceIncludingTax($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_INCLUDING_TAX;
    }

    public function displaySalesPriceExcludingTax($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_EXCLUDING_TAX;
    }

    public function displaySalesBothPrices($scopeId = null): bool
    {
        return $this->getSalesPriceDisplayType($scopeId) === self::DISPLAY_TYPE_BOTH;
    }

    public function getSalesPriceDisplayType($scopeId = null): int
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_DISPLAY_SALES_SHIPPING,
            ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }
}
