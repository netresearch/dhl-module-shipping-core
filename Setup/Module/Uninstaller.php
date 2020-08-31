<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
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

    /**
     * @param EavSetup $eavSetup
     */
    public static function deleteAttributes(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(Product::ENTITY, DGCategory::CODE);
        $eavSetup->removeAttribute(Product::ENTITY, TariffNumber::CODE);
        $eavSetup->removeAttribute(Product::ENTITY, ExportDescription::CODE);
    }
}
