<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Model\Package;
use Dhl\ShippingCore\Model\PackageCollection;
use Dhl\ShippingCore\Model\PackageCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Shipping\Helper\Carrier;
use Magento\Shipping\Model\Config as ShippingConfig;
use Magento\Store\Model\ScopeInterface;

/**
 * Class Config
 *
 * @package Dhl\ShippingCore\Model\Config
 */
class Config implements ConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PackageCollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param PackageCollectionFactory $collectionFactory
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        PackageCollectionFactory $collectionFactory
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->packageCollectionFactory = $collectionFactory;
    }

    /**
     * Get payment methods that were marked as cash on delivery methods in configuration
     *
     * @param mixed $store
     * @return string[]
     */
    public function getCodMethods($store = null): array
    {
        $paymentMethods = $this->scopeConfig->getValue(
            self::CONFIG_PATH_COD_METHODS,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        if (empty($paymentMethods)) {
            return [];
        }

        return explode(',', $paymentMethods);
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
     * Get terms of trade.
     *
     * @param mixed $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_TERMS_OF_TRADE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the cut off time.
     *
     * @param mixed $store
     * @return string
     */
    public function getCutOffTime($store = null): string
    {
        return (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_CUT_OFF_TIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the general weight unit.
     *
     * @param mixed $store
     * @return string
     */
    public function getWeightUnit($store = null): string
    {
        $weightUOM = $this->scopeConfig->getValue(
            self::CONFIG_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeWeightUOM($weightUOM);
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
     * Checks if route is dutiable by stores origin country and eu country list
     *
     * @param string $receiverCountry
     * @param mixed $store
     * @return bool
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
        $euCountries = $this->scopeConfig->getValue(
            Carrier::XML_PATH_EU_COUNTRIES_LIST,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return explode(',', $euCountries);
    }

    /**
     * Returns the shipping origin country
     *
     * @see ShippingConfig
     *
     * @param mixed  $store
     * @param string $scope
     *
     * @return string
     */
    public function getOriginCountry($store = null, $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string)$this->scopeConfig->getValue(
            ShippingConfig::XML_PATH_ORIGIN_COUNTRY_ID,
            $scope,
            $store
        );
    }

    /**
     * Get PackageCollection of My Own Packages
     *
     * @param mixed $store
     * @return PackageCollection
     */
    public function getOwnPackages($store = null): PackageCollection
    {
        /** @var mixed[] $configValue */
        $configValue = $this->serializer->unserialize(
            $this->scopeConfig->getValue(
                self::CONFIG_PATH_OWN_PACKAGES,
                ScopeInterface::SCOPE_STORE,
                $store
            )
        );

        ksort($configValue);
        $default = array_pop($configValue);
        /** @var PackageCollection $collection */
        $collection = $this->packageCollectionFactory->create();
        foreach ($configValue as $key => $packageData) {
            $packageData[Package::KEY_IS_DEFAULT] = $key === $default;
            $packageData[Package::KEY_ID] = $key;
            $collection->addPackageAsArray($packageData);
        }

        return $collection;
    }

    /**
     * @param mixed $store
     * @return Package|null
     */
    public function getOwnPackagesDefault($store = null)
    {
        $collection = $this->getOwnPackages($store);

        return $collection->getDefaultPackage();
    }

    /**
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     */
    public function getCarrierTitleByCode(string $carrierCode, $store = null): string
    {
        return $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/title',
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * @param mixed $store
     * @return string
     */
    public function getRawWeightUnit($store = null): string
    {
        $weightUnit =  $this->scopeConfig->getValue(
            self::CONFIG_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->normalizeRawWeight($weightUnit);
    }

    /**
     * @param string $weightUnit
     * @return string
     */
    public function normalizeRawWeight(string $weightUnit): string
    {
        $weightUnit = (strtoupper($weightUnit) === \Zend_Measure_Weight::LBS)
            ? \Zend_Measure_Weight::POUND
            : \Zend_Measure_Weight::KILOGRAM;

        return $weightUnit;
    }

    /**
     * @param string $weightUnit
     * @return string
     */
    public function getRawDimensionUnit(string $weightUnit): string
    {
        $dimensionUnit = (strtoupper($weightUnit) === \Zend_Measure_Weight::POUND)
            ? \Zend_Measure_Length::INCH
            : \Zend_Measure_Length::CENTIMETER;

        return $dimensionUnit;
    }

    /**
     * Check whether or not failed shipments should be automatically retried during bulk/cron processing.
     *
     * @param mixed $store
     * @return bool
     */
    public function isBulkRetryEnabled($store = null): bool
    {
        return $this->scopeConfig->isSetFlag(
            self::CONFIG_PATH_AUTORETRY_FAILED,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Check whether or not a shipment confirmation email should be sent after successful bulk/cron processing.
     *
     * @param mixed $store
     * @return bool
     */
    public function isBulkNotificationEnabled($store = null): bool
    {
        return (bool) $this->scopeConfig->getValue(
            self::CONFIG_PATH_AUTOCREATE_NOTIFY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the default item category for international shipments.
     *
     * @return string
     */
    public function getDefaultExportContentType(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_CONTENT_TYPE
        );
    }

    /**
     * For item category "OTHER", get the default explanation (e.g. "Merchandise").
     *
     * @return string
     */
    public function getDefaultExportContentExplanation(): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_CONTENT_EXPLANATION
        );
    }
}
