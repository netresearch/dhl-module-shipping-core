<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Shipping\Helper\Carrier;
use Magento\Shipping\Model\Config;
use Magento\Store\Model\ScopeInterface;

/**
 * Class CoreConfig
 *
 * @package Dhl\ShippingCore\Model\Config
 */
class CoreConfig implements CoreConfigInterface
{
    public const DEFAULT_DIMENSION_UNIT = 'in';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfigInterface;

    /**
     * @var string[]
     */
    private $weightUnitMap = [
        'kgs' => 'kg',
        'lbs' => 'lb',
        'POUND' => 'lb',
        'KILOGRAM' => 'kg',
    ];

    /**
     * @var string[]
     */
    private $dimensionUnitMap = [
        'INCH' => 'in',
        'CENTIMETER' => 'cm',
    ];

    /**
     * @var string[]
     */
    private $weightUnitToDimensionUnitMap = [
        'kg' => 'cm',
        'lb' => 'in',
    ];

    /**
     * CoreConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfigInterface
     */
    public function __construct(ScopeConfigInterface $scopeConfigInterface)
    {
        $this->scopeConfigInterface = $scopeConfigInterface;
    }

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
    public function getCodMethods($store = null): array
    {
        return explode(
            ',',
            $this->scopeConfigInterface->getValue(
                self::CONFIG_XML_PATH_COD_METHODS,
                ScopeInterface::SCOPE_WEBSITE,
                $store
            )
        );
    }

    /**
     * Check whether a payment method code was marked as cash on delivery method
     *
     * @param string $methodCode
     * @param mixed $store
     * @return bool
     */
    public function isCodPaymentMethod(string $methodCode, $store = null): bool
    {
        return \in_array($methodCode, $this->getCodMethods($store), true);
    }

    /**
     * Get COD payment methods.
     *
     * @param null $store
     * @return string[]
     */
    public function getPaymentMethods($store = null): array
    {
        $paymentMethods = $this->scopeConfigInterface->getValue(
            self::CONFIG_XML_PATH_PAYMENT_METHODS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return explode(',', $paymentMethods);
    }

    /**
     * Get terms of trade.
     *
     * @param null $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string
    {
        return (string)$this->scopeConfigInterface->getValue(
            self::CONFIG_XML_PATH_TERMS_OF_TRADE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the cut off time.
     *
     * @param null $store
     * @return string
     */
    public function getCutOffTime($store = null): string
    {
        return (string)$this->scopeConfigInterface->getValue(
            self::CONFIG_XML_PATH_CUT_OFF_TIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the general weight unit.
     *
     * @param null $store
     * @return string
     */
    public function getWeightUnit($store = null): string
    {
        $weightUOM = $this->scopeConfigInterface->getValue(
            self::CONFIG_XML_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeWeightUOM($weightUOM);
    }

    /**
     * Get the general dimensions unit.
     *
     * @return string
     */
    public function getDimensionsUOM(): string
    {
        return $this->getDimensionsUOMfromWeightUOM(
            $this->getWeightUnit()
        );
    }

    /**
     * Maps Magento's internal unit names to SDKs unit names
     *
     * @param string $unit
     * @return string
     */
    public function normalizeDimensionUOM(string $unit): string
    {
        if (array_key_exists($unit, $this->dimensionUnitMap)) {
            return $this->dimensionUnitMap[$unit];
        }

        return $unit;
    }

    /**
     * Maps Magento's internal unit names to SDKs unit names
     *
     * @param string $unit
     * @return string
     */
    public function normalizeWeightUOM(string $unit): string
    {
        if (array_key_exists($unit, $this->weightUnitMap)) {
            return $this->weightUnitMap[$unit];
        }

        return $unit;
    }

    /**
     * Derives the current dimensions UOM from weight UOM (so both UOMs are in SU or SI format, but always consistent)
     *
     * @param $unit
     * @return string
     */
    private function getDimensionsUOMfromWeightUOM($unit): string
    {
        if (array_key_exists($unit, $this->weightUnitToDimensionUnitMap)) {
            return $this->weightUnitToDimensionUnitMap[$unit];
        }

        return self::DEFAULT_DIMENSION_UNIT;
    }

    /**
     * Checks if route is dutiable by stores origin country and eu country list
     *
     * @param string $receiverCountry
     * @param mixed $store
     * @return bool
     *
     */
    public function isDutiableRoute(string $receiverCountry, $store = null): bool
    {
        $originCountry = $this->getOriginCountry($store);
        $euCountries = $this->getEuCountries($store);

        $bothEU = \in_array($originCountry, $euCountries, true) && \in_array($receiverCountry, $euCountries, true);

        return $receiverCountry !== $originCountry && !$bothEU;
    }

    /**
     * Returns countries that are marked as EU-Countries
     *
     * @param mixed $store
     * @return string[]
     */
    public function getEuCountries($store = null): array
    {
        $euCountries = $this->scopeConfigInterface->getValue(
            Carrier::XML_PATH_EU_COUNTRIES_LIST,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return explode(',', $euCountries);
    }

    /**
     * Returns the shipping origin country
     *
     * @see Config
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginCountry($store = null): string
    {
        return (string)$this->scopeConfigInterface->getValue(
            Config::XML_PATH_ORIGIN_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOwnPackages(?string $store = null): string
    {
        return (string)$this->scopeConfigInterface->getValue(
            self::CONFIG_XML_PATH_OWN_PACKAGES,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOwnPackagesDefault(?string $store = null): string
    {
        // TODO: Implement getOwnPackagesDefault() method.
    }
}
