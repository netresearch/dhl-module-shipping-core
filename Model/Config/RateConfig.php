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

    /**
     * @inheritDoc
     */
    public function getAllowedProducts(string $carrierCode, $store = null): array
    {
        $allowedProducts = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_ALLOWED_PRODUCTS),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeAllowedProducts((string) $allowedProducts);
    }

    /**
     * @inheritDoc
     */
    public function isRatesConfigurationEnabled(string $carrierCode, $store = null): bool
    {
        $value = $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_AFFECT_RATES),
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $value === '1';
    }

    /**
     * @inheritDoc
     */
    public function getHandlingFee(string $carrierCode, $store = null): float
    {
        if (!$this->isRatesConfigurationEnabled($carrierCode, $store)) {
            return 0;
        }

        $type = $this->getHandlingType($carrierCode, $store) === AbstractCarrier::HANDLING_TYPE_FIXED
            ? self::CONFIG_XML_SUFFIX_FIXED
            : self::CONFIG_XML_SUFFIX_PERCENTAGE;

        return (float) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_HANDLING_FEE) . $type,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @inheritDoc
     */
    public function getHandlingType(string $carrierCode, $store = null): string
    {
        if (!$this->isRatesConfigurationEnabled($carrierCode, $store)) {
            return '';
        }

        return (string) $this->scopeConfig->getValue(
            $this->getConfigPathByCarrierCode($carrierCode, self::CONFIG_XML_PATH_HANDLING_TYPE),
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
