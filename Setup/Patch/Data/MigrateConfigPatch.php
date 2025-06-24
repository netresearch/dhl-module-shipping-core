<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Magento\Framework\Setup\Patch\DataPatchInterface;
use Netresearch\ShippingCore\Model\Config\BatchProcessingConfig;
use Netresearch\ShippingCore\Model\Config\ParcelProcessingConfig;
use Netresearch\ShippingCore\Setup\Patch\Data\Migration\Config;

class MigrateConfigPatch implements DataPatchInterface
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    #[\Override]
    public static function getDependencies(): array
    {
        return [];
    }

    #[\Override]
    public function getAliases(): array
    {
        return [];
    }

    /**
     * Migrate config values from dhl/module-shipping-core version 1.
     *
     * phpcs:disable Generic.Files.LineLength.TooLong
     *
     * @return void
     * @throws \Exception
     */
    #[\Override]
    public function apply()
    {
        $this->config->migrate([
            'dhlshippingsolutions/dhlglobalwebservices/cod_methods' => ParcelProcessingConfig::CONFIG_PATH_COD_METHODS,
            'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/export_content_type' => ParcelProcessingConfig::CONFIG_PATH_CONTENT_TYPE,
            'dhlshippingsolutions/dhlglobalwebservices/shipment_defaults/export_content_explanation' => ParcelProcessingConfig::CONFIG_PATH_CONTENT_EXPLANATION,
            'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/cron_enabled' => BatchProcessingConfig::CONFIG_PATH_CRON_ENABLED,
            'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/cron_order_status' => BatchProcessingConfig::CONFIG_PATH_CRON_ORDER_STATUS,
            'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/retry_failed_shipments' => BatchProcessingConfig::CONFIG_PATH_RETRY_FAILED,
            'dhlshippingsolutions/dhlglobalwebservices/bulk_settings/autocreate_notify' => BatchProcessingConfig::CONFIG_PATH_NOTIFY_CUSTOMER,
        ]);
    }
}
