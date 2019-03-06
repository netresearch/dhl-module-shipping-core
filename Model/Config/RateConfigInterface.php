<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

/**
 * Interface RateConfigInterface
 *
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
 */
interface RateConfigInterface
{
    const CONFIG_ROOT = 'carriers/%s/';

    // 400_checkout_presentation.xml
    const CONFIG_XML_PATH_ROUNDED_PRICES_FORMAT = self::CONFIG_ROOT . 'checkout_settings/round_prices_format';
    const CONFIG_XML_PATH_ROUNDED_PRICES_STATIC_DECIMAL = self::CONFIG_ROOT . 'checkout_settings/round_prices_static_decimal';
    const CONFIG_XML_PATH_ROUNDED_PRICES_MODE = self::CONFIG_ROOT . 'checkout_settings/round_prices_mode';

    // 500_shipping_markup.xml
    const CONFIG_XML_PATH_INTERNATIONAL_HANDLING_TYPE = self::CONFIG_ROOT . 'shipping_markup/international_handling_type';
    const CONFIG_XML_PATH_INTERNATIONAL_HANDLING_FEE = self::CONFIG_ROOT . 'shipping_markup/international_handling_fee';
    const CONFIG_XML_PATH_DOMESTIC_HANDLING_TYPE = self::CONFIG_ROOT . 'shipping_markup/domestic_handling_type';
    const CONFIG_XML_PATH_DOMESTIC_HANDLING_FEE = self::CONFIG_ROOT . 'shipping_markup/domestic_handling_fee';
    const CONFIG_XML_PATH_DOMESTIC_AFFECT_RATES = self::CONFIG_ROOT . 'shipping_markup/domestic_affect_rates';
    const CONFIG_XML_PATH_INTERNATIONAL_AFFECT_RATES = self::CONFIG_ROOT . 'shipping_markup/international_affect_rates';
    const CONFIG_XML_SUFFIX_FIXED = '_fixed';
    const CONFIG_XML_SUFFIX_PERCENTAGE = '_percentage';

    /**
     * Get rounded prices format.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string
     */
    public function getRoundedPricesFormat(string $carrierCode, $store = null): string;

    /**
     * Returns true when price should be rounded up.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return bool
     */
    public function roundUp(string $carrierCode, $store = null): bool;

    /**
     * Get rounded prices static value.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return float
     */
    public function getRoundedPricesStaticDecimal(string $carrierCode, $store = null): float;

    /**
     * Get mode for rounded prices.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return string|null
     */
    public function getRoundedPricesMode(string $carrierCode, $store = null): string;

    /**
     * Returns true when price should be rounded off.
     *
     * @param string $carrierCode
     * @param string|null $store
     *
     * @return bool
     */
    public function roundOff(string $carrierCode, $store = null): bool;

    /**
     * Get the domestic handling type.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return string
     */
    public function getDomesticHandlingType(string $carrierCode, $store = null): string;

    /**
     * Get the domestic handling fee.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return float
     */
    public function getDomesticHandlingFee(string $carrierCode, $store = null): float;

    /**
     * Get the international handling type.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return string
     */
    public function getInternationalHandlingType(string $carrierCode, $store = null): string;

    /**
     * Get the international handling fee.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return float
     */
    public function getInternationalHandlingFee(string $carrierCode, $store = null): float;
}
