<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Module\Setup;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstaller
{
    /**
     * Delete all config data related to Dhl_ShippingCore.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function deleteConfig(SchemaSetupInterface $schemaSetup)
    {
        $defaultConnection = $schemaSetup->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $configTable = $schemaSetup->getTable('core_config_data', ResourceConnection::DEFAULT_CONNECTION);
        $defaultConnection->delete($configTable, "`path` LIKE 'shipping/dhlglobalwebservices/%'");
    }
}
