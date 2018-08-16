<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Model\Package;
use Dhl\ShippingCore\Model\PackageCollection;
use Magento\Shipping\Model\Config;

/**
 * Interface CoreConfigInterface
 *
 * @package Dhl\ShippingCore\Model\Config
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
interface CoreConfigInterface
{
    const CONFIG_ROOT = 'shipping/dhlglobalwebservices/';

    const CONFIG_XML_PATH_COD_METHODS = self::CONFIG_ROOT . 'cod_methods';
    const CONFIG_XML_PATH_PAYMENT_METHODS = self::CONFIG_ROOT . 'shipment_dhlcodmethods';
    const CONFIG_XML_PATH_TERMS_OF_TRADE = self::CONFIG_ROOT . 'terms_of_trade';
    const CONFIG_XML_PATH_CUT_OFF_TIME = self::CONFIG_ROOT . 'cut_off_time';

    const CONFIG_XML_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';
    const CONFIG_XML_PATH_OWN_PACKAGES = self::CONFIG_ROOT . 'package_dimension';

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
    public function getCodMethods($store = null): array;

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, $store = null): bool;

    /**
     * Get COD payment methods.
     *
     * @param null $store
     * @return string[]
     */
    public function getPaymentMethods($store = null): array;

    /**
     * Get terms of trade.
     *
     * @param null $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string;

    /**
     * Get the cut off time.
     *
     * @param null $store
     * @return string
     */
    public function getCutOffTime($store = null): string;

    /**
     * Get the general weight unit.
     *
     * @param null $store
     * @return string
     */
    public function getWeightUnit($store = null): string;

    /**
     * Get the general dimensions unit.
     *
     * @return string
     */
    public function getDimensionsUOM(): string;

    /**
     * Checks if route is dutiable by stores origin country and eu country list
     *
     * @param string $receiverCountry
     * @param mixed $store
     * @return bool
     *
     */
    public function isDutiableRoute(string $receiverCountry, $store = null): bool;

    /**
     * Returns countries that are marked as EU-Countries
     *
     * @param mixed $store
     * @return string[]
     */
    public function getEuCountries($store = null): array;

    /**
     * Returns the shipping origin country
     *
     * @see Config
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginCountry($store = null): string;

    /**
     * Maps Magento's internal unit names to SDKs unit names
     *
     * @param string $unit
     * @return string
     */
    public function normalizeDimensionUOM(string $unit): string;

    /**
     * Maps Magento's internal unit names to SDKs unit names
     *
     * @param string $unit
     * @return string
     */
    public function normalizeWeightUOM(string $unit): string;

    /**
     * @param null|string $store
     * @return PackageCollection
     */
    public function getOwnPackages(?string $store = null): PackageCollection;

    /**
     * @param null|string $store
     * @return Package|null
     */
    public function getOwnPackagesDefault(?string $store = null): ?Package;
}
