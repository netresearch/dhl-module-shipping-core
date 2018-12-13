<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\Express\Model\Config\Source\InternationalProducts;
use Dhl\ShippingCore\Model\Config\Source\RoundedPricesFormat;
use Dhl\ShippingCore\Model\Config\Source\RoundedPricesMode;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Store\Model\ScopeInterface;

/**
 * Class RateConfig
 *
 * @package Dhl\ShippingCore\Model\Config
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class RateConfig implements RateConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ModuleConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * Get the allowed domestic products.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string[]
     */
    public function getAllowedDomesticProducts($carrierCode, $store = null): array
    {
        $allowedProducts = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ALLOWED_DOMESTIC_PRODUCTS),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeAllowedProducts($allowedProducts);
    }

    /**
     * Get the allowed international products.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string[]
     */
    public function getAllowedInternationalProducts($carrierCode, $store = null): array
    {
        $allowedProductsValue = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ALLOWED_INTERNATIONAL_PRODUCTS),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeAllowedProducts($allowedProductsValue);
    }

    /**
     * Get the domestic handling type.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string
     */
    public function getDomesticHandlingType($carrierCode, $store = null): string
    {
        if (!$this->isDomesticRatesConfigurationEnabled($carrierCode, $store)) {
            return '';
        }

        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_HANDLING_TYPE),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the domestic handling fee.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return float
     */
    public function getDomesticHandlingFee($carrierCode, $store = null): float
    {
        if (!$this->isDomesticRatesConfigurationEnabled($carrierCode, $store)) {
            return 0;
        }

        $type = $this->getDomesticHandlingType($carrierCode, $store) ===
        AbstractCarrier::HANDLING_TYPE_FIXED ?
            self::CONFIG_XML_SUFFIX_FIXED :
            self::CONFIG_XML_SUFFIX_PERCENTAGE;

        return (float)$this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_HANDLING_FEE) . $type,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the international handling type.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string
     */
    public function getInternationalHandlingType($carrierCode, $store = null): string
    {
        if (!$this->isInternationalRatesConfigurationEnabled($carrierCode, $store)) {
            return '';
        }

        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_HANDLING_TYPE),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the international handling fee.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return float
     */
    public function getInternationalHandlingFee($carrierCode, $store = null): float
    {
        if (!$this->isInternationalRatesConfigurationEnabled($carrierCode, $store)) {
            return 0;
        }

        $type = $this->getInternationalHandlingType($carrierCode, $store) ===
        AbstractCarrier::HANDLING_TYPE_FIXED ?
            self::CONFIG_XML_SUFFIX_FIXED :
            self::CONFIG_XML_SUFFIX_PERCENTAGE;

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_HANDLING_FEE) . $type,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param string| null $store
     * @return bool
     */
    public function isInternationalRatesConfigurationEnabled($carrierCode, $store = null): bool
    {
        $value = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_AFFECT_RATES),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value === '1';
    }

    /**
     * Returns true when price should be rounded up.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return bool
     */
    public function roundUp($carrierCode, $store = null): bool
    {
        return $this->getRoundedPricesFormat($carrierCode, $store) === RoundedPricesFormat::DO_NOT_ROUND
            ? false
            : $this->getRoundedPricesMode($carrierCode, $store) === RoundedPricesMode::ROUND_UP;
    }

    /**
     * Get rounded prices format.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string
     */
    public function getRoundedPricesFormat($carrierCode, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_FORMAT),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get rounded prices static decimal value.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return float
     */
    public function getRoundedPricesStaticDecimal($carrierCode, $store = null): float
    {
        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_STATIC_DECIMAL),
            ScopeInterface::SCOPE_STORE,
            $store
        ) / 100;
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return bool
     */
    public function isInternationalFreeShippingEnabled($carrierCode, $store = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_FREE_SHIPPING_INTERNATIONAL_ENABLED),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return bool
     */
    public function isDomesticFreeShippingEnabled($carrierCode, $store = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_FREE_SHIPPING_DOMESTIC_ENABLED),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return bool
     */
    public function isFreeShippingVirtualProductsIncluded($carrierCode, $store = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_FREE_SHIPPING_VIRTUAL_ENABLED),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return array
     */
    public function getDomesticFreeShippingProducts($carrierCode, $store = null): array
    {
        if (!$this->isDomesticFreeShippingEnabled($carrierCode, $store)) {
            return [];
        }

        $allowedProducts = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_FREE_SHIPPING_PRODUCTS),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeAllowedProducts($allowedProducts);
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return float
     */
    public function getDomesticFreeShippingSubTotal($carrierCode, $store = null): float
    {
        if (!$this->isDomesticFreeShippingEnabled($carrierCode, $store)) {
            return 0;
        }

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_FREE_SHIPPING_SUBTOTAL),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return array
     */
    public function getInternationalFreeShippingProducts($carrierCode, $store = null): array
    {
        if (!$this->isInternationalFreeShippingEnabled($carrierCode, $store)) {
            return [];
        }

        $allowedProducts = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_FREE_SHIPPING_PRODUCTS),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeAllowedProducts($allowedProducts);
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return float
     */
    public function getInternationalFreeShippingSubTotal($carrierCode, $store = null): float
    {
        if (!$this->isInternationalFreeShippingEnabled($carrierCode, $store)) {
            return 0;
        }

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_FREE_SHIPPING_SUBTOTAL),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Returns true when price should be rounded off.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return bool
     */
    public function roundOff($carrierCode, $store = null): bool
    {
        return $this->getRoundedPricesFormat($carrierCode, $store) === RoundedPricesFormat::DO_NOT_ROUND
            ? false
            : $this->getRoundedPricesMode($carrierCode, $store) === RoundedPricesMode::ROUND_OFF;
    }

    /**
     * Get mode for rounded prices.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string
     */
    public function getRoundedPricesMode($carrierCode, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_MODE),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param string $carrierCode
     * @param null $store
     * @return bool
     */
    public function isDomesticRatesConfigurationEnabled($carrierCode, $store = null): bool
    {
        $value = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_AFFECT_RATES),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value === '1';
    }

    /**
     * @param string $carrierCode
     * @param string $path
     * @return string
     */
    private function getConfigPathByCarrierCode($carrierCode, $path): string
    {
        return sprintf($path, $carrierCode);
    }

    /**
     * Resolves and flattens product codes separated by ";".
     *
     * @param string $allowedProductsValue The ";" separated list of product codes
     *
     * @return string[]
     *
     * @see InternationalProducts
     */
    private function normalizeAllowedProducts(string $allowedProductsValue): array
    {
        $combinedKeys = explode(',', $allowedProductsValue) ?: [];

        return array_reduce(
            $combinedKeys,
            function ($carry, $item) {
                $singleKeys = explode(';', $item);
                if ($singleKeys !== false) {
                    $carry = array_merge($carry, $singleKeys);
                }

                return $carry;
            },
            []
        );
    }
}
