<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoresConfig;

/**
 * Class CronConfig
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CronConfig
{
    const CONFIG_PATH_CRON_ENABLED = 'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/cron_enabled';
    const CONFIG_PATH_CRON_ORDER_STATUS = 'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/cron_order_status';

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StoresConfig
     */
    private $storesConfig;

    /**
     * CronConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param StoresConfig $storesConfig
     */
    public function __construct(ScopeConfigInterface $scopeConfig, StoresConfig $storesConfig)
    {
        $this->scopeConfig = $scopeConfig;
        $this->storesConfig = $storesConfig;
    }

    /**
     * Obtain the stores which are enabled for cron auto-create.
     *
     * @return int[]
     */
    public function getAutoCreateStores(): array
    {
        $storesConfig = $this->storesConfig->getStoresConfigByPath(self::CONFIG_PATH_CRON_ENABLED);
        $activeStores = array_filter($storesConfig);

        return array_keys($activeStores);
    }

    /**
     * Get allowed order statuses for cron auto-create
     *
     * @return string Comma-separated list of order status
     */
    public function getAutoCreateOrderStatus(): string
    {
        return $this->scopeConfig->getValue(self::CONFIG_PATH_CRON_ORDER_STATUS);
    }
}
