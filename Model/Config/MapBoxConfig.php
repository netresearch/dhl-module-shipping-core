<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Netresearch\ShippingCore\Api\Config\MapBoxConfigInterface;

class MapBoxConfig implements MapBoxConfigInterface
{
    private const CONFIG_PATH_LOCATION_FINDER_API_TOKEN = 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/maptile_api_token';
    private const CONFIG_PATH_LOCATION_FINDER_URL = 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/maptile_url';
    private const CONFIG_PATH_LOCATION_FINDER_ATTRIBUTION = 'dhlshippingsolutions/dhlglobalwebservices/locationfinder_settings/map_copyright_attribution';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    #[\Override]
    public function getApiToken($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_LOCATION_FINDER_API_TOKEN,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getMapTileUrl($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_LOCATION_FINDER_URL,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    #[\Override]
    public function getMapAttribution($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            self::CONFIG_PATH_LOCATION_FINDER_ATTRIBUTION,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }
}
