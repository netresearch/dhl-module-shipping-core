<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\UnitConverterInterface;
use Dhl\ShippingCore\Model\Package;
use Dhl\ShippingCore\Model\PackageCollection;
use Dhl\ShippingCore\Model\PackageCollectionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
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
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var PackageCollectionFactory
     */
    private $packageCollectionFactory;

    /**
     * @var UnitConverterInterface
     */
    private $unitConverter;

    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * Config constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     * @param PackageCollectionFactory $packageCollectionFactory
     * @param UnitConverterInterface $unitConverter
     * @param TimezoneInterface $timezone
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        SerializerInterface $serializer,
        PackageCollectionFactory $packageCollectionFactory,
        UnitConverterInterface $unitConverter,
        TimezoneInterface $timezone
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->serializer = $serializer;
        $this->packageCollectionFactory = $packageCollectionFactory;
        $this->unitConverter = $unitConverter;
        $this->timezone = $timezone;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_VERSION);
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
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_TERMS_OF_TRADE,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    /**
     * Get the cut off time.
     *
     * @param mixed $store
     * @return \DateTime
     */
    public function getCutOffTime($store = null): \DateTime
    {
        $cutOffTimeRaw = (string)$this->scopeConfig->getValue(
            self::CONFIG_PATH_CUT_OFF_TIME,
            ScopeInterface::SCOPE_STORE,
            $store
        );
        $cutOffTimeParts  = explode(
            ',',
            $cutOffTimeRaw
        );

        list($hours, $minutes, $seconds) = array_map('intval', $cutOffTimeParts);

        return $this->timezone->scopeDate($store)->setTime($hours, $minutes, $seconds);
    }

    /**
     * Get the general weight unit.
     *
     * @param int|string|null $store
     * @return string - either kg or lb
     */
    public function getWeightUnit($store = null): string
    {
        $weightUOM = $this->scopeConfig->getValue(
            self::CONFIG_PATH_WEIGHT_UNIT,
            ScopeInterface::SCOPE_STORE,
            $store
        );

        return $this->unitConverter->normalizeWeightUnit($weightUOM);
    }

    /**
     * Get the normalized dimension unit
     *
     * @param int|string|null $store
     * @return string - either cm or in
     */
    public function getDimensionUnit($store = null): string
    {
        $weightUOM = $this->getWeightUnit($store);

        return $weightUOM === 'kg' ? 'cm' : 'in';
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
     * @param mixed $store
     * @param string $scope
     *
     * @return string
     * @see ShippingConfig
     *
     */
    public function getOriginCountry($store = null, $scope = ScopeInterface::SCOPE_STORE): string
    {
        return (string) $this->scopeConfig->getValue(
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
