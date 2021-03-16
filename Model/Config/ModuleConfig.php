<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Netresearch\ShippingCore\Api\InfoBox\VersionInterface;

class ModuleConfig implements VersionInterface
{
    public const CONFIG_PATH_VERSION = 'dhlshippingsolutions/version';

    public const CONFIG_PATH_TERMS_OF_TRADE = 'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/terms_of_trade';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_VERSION);
    }
}
