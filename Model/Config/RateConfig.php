<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Model\Config\Source\RoundedPricesFormat;
use Dhl\ShippingCore\Model\Config\Source\RoundedPricesMode;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Magento\Store\Model\ScopeInterface;

/**
 * Class RateConfig
 *
 * @author    Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
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
     * @param string $carrierCode
     * @param string $path
     *
     * @return string
     */
    private function getConfigPathByCarrierCode(string $carrierCode, string $path): string
    {
        return sprintf($path, $carrierCode);
    }

    /**
     * @inheritDoc
     */
    public function roundUp(string $carrierCode, $store = null): bool
    {
        return $this->getRoundedPricesFormat($carrierCode, $store) === RoundedPricesFormat::DO_NOT_ROUND
            ? false
            : $this->getRoundedPricesMode($carrierCode, $store) === RoundedPricesMode::ROUND_UP;
    }

    /**
     * @inheritDoc
     */
    public function getRoundedPricesFormat(string $carrierCode, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_FORMAT),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getRoundedPricesMode(string $carrierCode, $store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_MODE),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getRoundedPricesStaticDecimal(string $carrierCode, $store = null): float
    {
        return (float) $this->scopeConfig->getValue(
                $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ROUNDED_PRICES_STATIC_DECIMAL),
                ScopeInterface::SCOPE_STORE,
                $store
            ) / 100;
    }

    /**
     * @inheritDoc
     */
    public function roundOff(string $carrierCode, $store = null): bool
    {
        return $this->getRoundedPricesFormat($carrierCode, $store) === RoundedPricesFormat::DO_NOT_ROUND
            ? false
            : $this->getRoundedPricesMode($carrierCode, $store) === RoundedPricesMode::ROUND_OFF;
    }

    /**
     * Check if domestic rates configuration is enabled.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return bool
     */
    private function isDomesticRatesConfigurationEnabled(string $carrierCode, $store = null): bool
    {
        $value = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_AFFECT_RATES),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value === '1';
    }

    /**
     * @inheritDoc
     */
    public function getDomesticHandlingType(string $carrierCode, $store = null): string
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
     * @inheritDoc
     */
    public function getDomesticHandlingFee(string $carrierCode, $store = null): float
    {
        if (!$this->isDomesticRatesConfigurationEnabled($carrierCode, $store)) {
            return 0;
        }

        $type = $this->getDomesticHandlingType($carrierCode, $store) === AbstractCarrier::HANDLING_TYPE_FIXED
            ? self::CONFIG_XML_SUFFIX_FIXED
            : self::CONFIG_XML_SUFFIX_PERCENTAGE;

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_DOMESTIC_HANDLING_FEE) . $type,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check if international rates configuration is enabled.
     *
     * @param string      $carrierCode The carrier code
     * @param string|null $store
     *
     * @return bool
     */
    private function isInternationalRatesConfigurationEnabled(string $carrierCode, $store = null): bool
    {
        $value = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_AFFECT_RATES),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value === '1';
    }

    /**
     * @inheritDoc
     */
    public function getInternationalHandlingType(string $carrierCode, $store = null): string
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
     * @inheritDoc
     */
    public function getInternationalHandlingFee(string $carrierCode, $store = null): float
    {
        if (!$this->isInternationalRatesConfigurationEnabled($carrierCode, $store)) {
            return 0;
        }

        $type = $this->getInternationalHandlingType($carrierCode, $store) === AbstractCarrier::HANDLING_TYPE_FIXED
            ? self::CONFIG_XML_SUFFIX_FIXED
            : self::CONFIG_XML_SUFFIX_PERCENTAGE;

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_INTERNATIONAL_HANDLING_FEE) . $type,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
