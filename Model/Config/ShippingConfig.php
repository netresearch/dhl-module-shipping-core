<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Dhl\ShippingCore\Api\ShippingConfigInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Sales\Model\Order\Shipment;
use Magento\Store\Model\ScopeInterface;

class ShippingConfig implements ShippingConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    public function getOriginCountry($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_COUNTRY_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOriginRegion($store = null): int
    {
        return (int) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOriginCity($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_CITY,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOriginPostcode($store = null): string
    {
        return (string) $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ZIP,
            ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getOriginStreet($store = null): array
    {
        $scope = ScopeInterface::SCOPE_STORE;

        return [
            (string) $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS1, $scope, $store),
            (string) $this->scopeConfig->getValue(Shipment::XML_PATH_STORE_ADDRESS2, $scope, $store),
        ];
    }
}
