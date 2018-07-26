<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Config;

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
     * Get terms of trade.
     *
     * @param null $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string;

    /**
     * Get the cut-off time.
     *
     * @param null $store
     * @return string
     */
    public function getCutOffTime($store = null): string;
}
