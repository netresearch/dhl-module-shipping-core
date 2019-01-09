<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

/**
 * Interface RateConfigInterface
 *
 * @package Dhl\ShippingCore\Model\Config
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
interface RateConfigInterface
{
    const CONFIG_ROOT = 'carriers/%s/';

    // 300_shipping_preferences.xml
    const CONFIG_XML_PATH_ALLOWED_INTERNATIONAL_PRODUCTS = self::CONFIG_ROOT . 'shipment_settings/allowedinternationalproducts';
    const CONFIG_XML_PATH_ALLOWED_DOMESTIC_PRODUCTS = self::CONFIG_ROOT . 'shipment_settings/alloweddomesticproducts';

    // 400_checkout_presentation.xml
    const CONFIG_XML_PATH_ROUNDED_PRICES_FORMAT = self::CONFIG_ROOT . 'checkout_settings/round_prices_format';
    const CONFIG_XML_PATH_ROUNDED_PRICES_STATIC_DECIMAL = self::CONFIG_ROOT . 'checkout_settings/round_prices_static_decimal';
    const CONFIG_XML_PATH_ROUNDED_PRICES_MODE = self::CONFIG_ROOT . 'checkout_settings/round_prices_mode';

    // 500_shipping_markup.xml
    const CONFIG_XML_PATH_INTERNATIONAL_HANDLING_TYPE = self::CONFIG_ROOT . 'shipping_markup/international_handling_type';
    const CONFIG_XML_PATH_INTERNATIONAL_HANDLING_FEE = self::CONFIG_ROOT . 'shipping_markup/international_handling_fee';
    const CONFIG_XML_PATH_DOMESTIC_HANDLING_TYPE = self::CONFIG_ROOT . 'shipping_markup/domestic_handling_type';
    const CONFIG_XML_PATH_DOMESTIC_HANDLING_FEE = self::CONFIG_ROOT . 'shipping_markup/domestic_handling_fee';
    const CONFIG_XML_SUFFIX_FIXED = '_fixed';
    const CONFIG_XML_SUFFIX_PERCENTAGE = '_percentage';
    const CONFIG_XML_PATH_DOMESTIC_AFFECT_RATES = self::CONFIG_ROOT . 'shipping_markup/domestic_affect_rates';
    const CONFIG_XML_PATH_INTERNATIONAL_AFFECT_RATES = self::CONFIG_ROOT . 'shipping_markup/international_affect_rates';

    // 600_free_shipping.xml
    const CONFIG_XML_PATH_FREE_SHIPPING_VIRTUAL_ENABLED = self::CONFIG_ROOT . 'free_shipping_settings/free_shipping_virtual_products_enable';
    const CONFIG_XML_PATH_FREE_SHIPPING_INTERNATIONAL_ENABLED = self::CONFIG_ROOT . 'free_shipping_settings/international_free_shipping_enable';
    const CONFIG_XML_PATH_INTERNATIONAL_FREE_SHIPPING_PRODUCTS = self::CONFIG_ROOT . 'free_shipping_settings/international_free_shipping_products';
    const CONFIG_XML_PATH_INTERNATIONAL_FREE_SHIPPING_SUBTOTAL = self::CONFIG_ROOT . 'free_shipping_settings/international_free_shipping_subtotal';
    const CONFIG_XML_PATH_FREE_SHIPPING_DOMESTIC_ENABLED = self::CONFIG_ROOT . 'free_shipping_settings/domestic_free_shipping_enable';
    const CONFIG_XML_PATH_DOMESTIC_FREE_SHIPPING_PRODUCTS = self::CONFIG_ROOT . 'free_shipping_settings/domestic_free_shipping_products';
    const CONFIG_XML_PATH_DOMESTIC_FREE_SHIPPING_SUBTOTAL = self::CONFIG_ROOT . 'free_shipping_settings/domestic_free_shipping_subtotal';

    /**
     * Get the allowed domestic products.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string[]
     */
    public function getAllowedDomesticProducts($carrierCode, $store = null): array;

    /**
     * Get the allowed international products.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string[]
     */
    public function getAllowedInternationalProducts($carrierCode, $store = null): array;

    /**
     * Get the domestic free shipping subtotal value.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return float
     */
    public function getDomesticFreeShippingSubTotal($carrierCode, $store = null): float;

    /**
     * Get the international free shipping subtotal value.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return float
     */
    public function getInternationalFreeShippingSubTotal($carrierCode, $store = null): float;

    /**
     * Returns whether free shipping is enabled for domestic products or not.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return bool
     */
    public function isDomesticFreeShippingEnabled($carrierCode, $store = null): bool;

    /**
     * Returns whether free shipping is enabled for international products or not.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return bool
     */
    public function isInternationalFreeShippingEnabled($carrierCode, $store = null): bool;

    /**
     * Returns whether virtual products should be included in the subtotal value calculation or not.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return bool
     */
    public function isFreeShippingVirtualProductsIncluded($carrierCode, $store = null): bool;

    /**
     * Get the domestic free shipping allowed products.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return string[]
     */
    public function getDomesticFreeShippingProducts($carrierCode, $store = null): array;

    /**
     * Get the international free shipping allowed products.
     *
     * @param string $carrierCode
     * @param string|null $store Store name
     *
     * @return string[]
     */
    public function getInternationalFreeShippingProducts($carrierCode, $store = null): array;

    /**
     * Get the domestic handling type.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string
     */
    public function getDomesticHandlingType($carrierCode, $store = null): string;

    /**
     * Get the international handling type.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string
     */
    public function getInternationalHandlingType($carrierCode, $store = null): string;

    /**
     * Get the domestic handling fee.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return float
     */
    public function getDomesticHandlingFee($carrierCode, $store = null): float;

    /**
     * Get the international handling fee.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return float
     */
    public function getInternationalHandlingFee($carrierCode, $store = null): float;

    /**
     * Get rounded prices format.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string
     */
    public function getRoundedPricesFormat($carrierCode, $store = null): string;

    /**
     * Returns true when price should be rounded up.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return bool
     */
    public function roundUp($carrierCode, $store = null): bool;

    /**
     * Get rounded prices static value.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return float
     */
    public function getRoundedPricesStaticDecimal($carrierCode, $store = null): float;

    /**
     * Get mode for rounded prices.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return string|null
     */
    public function getRoundedPricesMode($carrierCode, $store = null): string;

    /**
     * Check if international rates configuration is enabled.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return bool
     */
    public function isInternationalRatesConfigurationEnabled($carrierCode, $store = null): bool;

    /**
     * Check if domestic rates configuration is enabled.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return bool
     */
    public function isDomesticRatesConfigurationEnabled($carrierCode, $store = null): bool;

    /**
     * Returns true when price should be rounded off.
     *
     * @param string $carrierCode
     * @param string|null $store
     * @return bool
     */
    public function roundOff($carrierCode, $store = null): bool;
}
