<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Model\Config\Source\RoundingFormat;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Store\Model\ScopeInterface;

/**
 * Class RateConfig
 *
 * @package Dhl\ShippingCore\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class RateConfig
{
    // rounding
    const CONFIG_PATH_USE_ROUNDING = 'dhlshippingsolutions/%s/rates_calculation/use_rounding';
    const CONFIG_PATH_ROUNDING_FORMAT = 'dhlshippingsolutions/%s/rates_calculation/rounding_group/number_format';
    const CONFIG_PATH_ROUNDING_DIRECTION = 'dhlshippingsolutions/%s/rates_calculation/rounding_group/direction';
    const CONFIG_PATH_ROUNDING_DECIMAL_VALUE = 'dhlshippingsolutions/%s/rates_calculation/rounding_group/decimal_value';

    // cross-border markup
    const CONFIG_PATH_USE_MARKUP_INTL = 'dhlshippingsolutions/%s/rates_calculation/use_markup_intl';
    const CONFIG_PATH_INTL_MARKUP_TYPE = 'dhlshippingsolutions/%s/rates_calculation/intl_markup_group/type';
    const CONFIG_PATH_INTL_MARKUP_AMOUNT = 'dhlshippingsolutions/%s/rates_calculation/intl_markup_group/amount';
    const CONFIG_PATH_INTL_MARKUP_PERCENTAGE = 'dhlshippingsolutions/%s/rates_calculation/intl_markup_group/percentage';

    // domestic markup
    const CONFIG_PATH_USE_MARKUP_DOMESTIC = 'dhlshippingsolutions/%s/rates_calculation/use_markup_domestic';
    const CONFIG_PATH_DOMESTIC_MARKUP_TYPE = 'dhlshippingsolutions/%s/rates_calculation/domestic_markup_group/type';
    const CONFIG_PATH_DOMESTIC_MARKUP_AMOUNT = 'dhlshippingsolutions/%s/rates_calculation/domestic_markup_group/amount';
    const CONFIG_PATH_DOMESTIC_MARKUP_PERCENTAGE = 'dhlshippingsolutions/%s/rates_calculation/domestic_markup_group/percentage';

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
     * Check if rates rounding is enabled for a given carrier.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return bool
     */
    public function isRoundingEnabled(string $carrierCode, $store = null): bool
    {
        $configPath = sprintf(self::CONFIG_PATH_USE_ROUNDING, $carrierCode);
        return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain rounding format for a given carrier.
     *
     * There are multiple supported ways to round a rate:
     * - round to a full integer value ("integer")
     * - round to a static floating point decimal ("static_decimal")
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     *
     * @see \Dhl\ShippingCore\Model\Config\Source\RoundingFormat
     */
    public function getRoundingFormat(string $carrierCode, $store = null): string
    {
        if (!$this->isRoundingEnabled($carrierCode, $store)) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_ROUNDING_FORMAT, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain rounding direction for a given carrier.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     *
     * @see \Dhl\ShippingCore\Model\Config\Source\RoundingDirection
     */
    public function getRoundingDirection(string $carrierCode, $store = null): string
    {
        if (!$this->isRoundingEnabled($carrierCode, $store)) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_ROUNDING_DIRECTION, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain the static decimal to round to.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     */
    public function getRoundingDecimal(string $carrierCode, $store = null): string
    {
        if (!$this->isRoundingEnabled($carrierCode, $store)) {
            return '';
        }

        $roundingFormat = $this->getRoundingFormat($carrierCode, $store);
        if ($roundingFormat !== RoundingFormat::DECIMAL) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_ROUNDING_DECIMAL_VALUE, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Check if markup calculation is enabled for cross-border routes.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return bool
     */
    public function isInternationalMarkupEnabled(string $carrierCode, $store = null): bool
    {
        $configPath = sprintf(self::CONFIG_PATH_USE_MARKUP_INTL, $carrierCode);
        return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup type (fixed, percentage) for cross-border routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getInternationalMarkupType(string $carrierCode, $store = null): string
    {
        if (!$this->isInternationalMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_INTL_MARKUP_TYPE, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup amount for cross-border routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getInternationalMarkupAmount(string $carrierCode, $store = null):string
    {
        if (!$this->isInternationalMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $markupType = $this->getInternationalMarkupType($carrierCode, $store);
        if ($markupType !== AbstractCarrier::HANDLING_TYPE_FIXED) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_INTL_MARKUP_AMOUNT, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup percentage for cross-border routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getInternationalMarkupPercentage(string $carrierCode, $store = null): string
    {
        if (!$this->isInternationalMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $markupType = $this->getInternationalMarkupType($carrierCode, $store);
        if ($markupType !== AbstractCarrier::HANDLING_TYPE_PERCENT) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_INTL_MARKUP_PERCENTAGE, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Check if markup calculation is enabled for domestic routes.
     *
     * @param string $carrierCode
     * @param mixed $store
     * @return bool
     */
    public function isDomesticMarkupEnabled(string $carrierCode, $store = null): bool
    {
        $configPath = sprintf(self::CONFIG_PATH_USE_MARKUP_DOMESTIC, $carrierCode);
        return $this->scopeConfig->isSetFlag($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup type (fixed, percentage) for domestic routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getDomesticMarkupType(string $carrierCode, $store = null): string
    {
        if (!$this->isDomesticMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_DOMESTIC_MARKUP_TYPE, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup amount for domestic routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getDomesticMarkupAmount(string $carrierCode, $store = null): string
    {
        if (!$this->isDomesticMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $markupType = $this->getDomesticMarkupType($carrierCode, $store);
        if ($markupType !== AbstractCarrier::HANDLING_TYPE_FIXED) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_DOMESTIC_MARKUP_AMOUNT, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * Obtain markup percentage for domestic routes.
     *
     * @param string $carrierCode
     * @param null $store
     * @return string
     */
    public function getDomesticMarkupPercentage(string $carrierCode, $store = null): string
    {
        if (!$this->isDomesticMarkupEnabled($carrierCode, $store)) {
            return '';
        }

        $markupType = $this->getDomesticMarkupType($carrierCode, $store);
        if ($markupType !== AbstractCarrier::HANDLING_TYPE_PERCENT) {
            return '';
        }

        $configPath = sprintf(self::CONFIG_PATH_DOMESTIC_MARKUP_PERCENTAGE, $carrierCode);
        return (string) $this->scopeConfig->getValue($configPath, ScopeInterface::SCOPE_STORE, $store);
    }
}
