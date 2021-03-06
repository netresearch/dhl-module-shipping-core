<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Model\ShippingBox\Package;
use Dhl\ShippingCore\Model\ShippingBox\PackageCollection;
use Magento\Store\Model\ScopeInterface;

/**
 * @api
 */
interface ConfigInterface
{
    const CONFIG_PATH_VERSION = 'dhlshippingsolutions/version';

    const CONFIG_PATH_COD_METHODS  = 'dhlshippingsolutions/dhlglobalwebservices/cod_methods';
    const CONFIG_PATH_CUT_OFF_TIME = 'dhlshippingsolutions/dhlglobalwebservices/cut_off_time';

    const CONFIG_PATH_WEIGHT_UNIT = 'general/locale/weight_unit';
    const CONFIG_PATH_OWN_PACKAGES = 'dhlshippingsolutions/dhlglobalwebservices/package_dimension';

    const CONFIG_PATH_AUTORETRY_FAILED  = 'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/retry_failed_shipments';
    const CONFIG_PATH_AUTOCREATE_NOTIFY = 'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/autocreate_notify';

    const CONFIG_PATH_TERMS_OF_TRADE      = 'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/terms_of_trade';
    const CONFIG_PATH_CONTENT_TYPE        = 'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/export_content_type';
    const CONFIG_PATH_CONTENT_EXPLANATION = 'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/export_content_explanation';

    const CONFIG_PATH_LOCATION_FINDER_API_TOKEN = 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/maptile_api_token';
    const CONFIG_PATH_LOCATION_FINDER_URL = 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/maptile_url';
    const CONFIG_PATH_LOCATION_FINDER_ATTRIBUTION= 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/map_copyright_attribution';

    /**
     * @return string
     */
    public function getModuleVersion(): string;

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
     * @param mixed $store
     * @return string
     */
    public function getTermsOfTrade($store = null): string;

    /**
     * Get the cut off time.
     *
     * @param mixed $store
     * @return \DateTime
     */
    public function getCutOffTime($store = null): \DateTime;

    /**
     * Get the general weight unit.
     *
     * @param mixed $store
     * @return string
     */
    public function getWeightUnit($store = null): string;

    /**
     * Get the normalized weight unit
     *
     * @param int|string|null $store
     * @return string - either cm or in
     */
    public function getDimensionUnit($store = null): string;

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
     * @deprecated since 1.1.0
     * @see ShippingConfigInterface::getOriginCountry
     *
     * @param mixed $store
     * @param string $scope
     *
     * @return string
     */
    public function getOriginCountry($store = null, $scope = ScopeInterface::SCOPE_STORE): string;

    /**
     * @param mixed $store
     * @return PackageCollection
     */
    public function getOwnPackages($store = null): PackageCollection;

    /**
     * @param mixed $store
     * @return Package|null
     */
    public function getOwnPackagesDefault($store = null);

    /**
     * @param string $carrierCode
     * @param mixed $store
     * @return string
     */
    public function getCarrierTitleByCode(string $carrierCode, $store = null): string;

    /**
     * Check whether or not failed shipments should be automatically retried during bulk/cron processing.
     *
     * @param mixed $store
     * @return bool
     */
    public function isBulkRetryEnabled($store = null): bool;

    /**
     * Check whether or not a shipment confirmation email should be sent after successful bulk/cron processing.
     *
     * @param mixed $store
     * @return bool
     */
    public function isBulkNotificationEnabled($store = null): bool;

    /**
     * Get the default item category for international shipments.
     *
     * @return string
     */
    public function getDefaultExportContentType(): string;

    /**
     * For item category "OTHER", get the default explanation (e.g. "Merchandise").
     *
     * @return string
     */
    public function getDefaultExportContentExplanation(): string;

    /**
     * @param mixed $store
     * @return string
     */
    public function getLocationFinderApiToken($store = null): string;

    /**
     * @param mixed $store
     * @return string
     */
    public function getLocationFinderMapTileUrl($store = null): string;

    /**
     * @param mixed $store
     * @return string
     */
    public function getLocationFinderMapAttribution($store = null): string;
}
