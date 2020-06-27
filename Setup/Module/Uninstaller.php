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
     * Remove label status column from orders grid.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function deleteLabelStatusColumn(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropColumn(
            $schemaSetup->getTable('sales_order_grid', Constants::SALES_CONNECTION_NAME),
            Constants::COLUMN_DHLGW_LABEL_STATUS
        );
    }

    /**
     * Drop label status table.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function dropLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_LABEL_STATUS);
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

    /**
     * Drop label status table.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function dropDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_RECIPIENT_STREET);
    }

    /**
     * Drop shipping option selection table
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @return void
     */
    public static function dropShippingOptionSelectionTables(SchemaSetupInterface $schemaSetup)
    {
        $checkoutConnection = $schemaSetup->getConnection(Constants::CHECKOUT_CONNECTION_NAME);
        $checkoutConnection->dropTable(Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION);

        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION);
    }

    /**
     * Remove shipping option fees
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @return void
     */
    public static function removeAdditionalFeeColumns(SchemaSetupInterface $schemaSetup)
    {
        $allTables = [
            Constants::CHECKOUT_CONNECTION_NAME => ['quote'],
            Constants::SALES_CONNECTION_NAME => [
                'sales_order',
                'sales_invoice',
                'sales_creditmemo',
            ],
        ];
        foreach ($allTables as $connection => $tables) {
            foreach ($tables as $table) {
                $adapter = $schemaSetup->getConnection($connection);
                $adapter
                    ->dropColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME
                    );
                $adapter
                    ->dropColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME
                    );
                $adapter
                    ->dropColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_FIELD_NAME
                    );
                $adapter
                    ->dropColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME
                    );
            }
        }
    }

    /**
     * Drop order item attribute table.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function dropOrderItemAttributeTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_ORDER_ITEM);
    }
}
