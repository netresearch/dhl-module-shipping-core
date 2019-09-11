<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * Class Uninstaller
 *
 * @package Dhl\ShippingCore\Setup
 */
class Uninstaller
{
    /**
     * Delete all config data related to Dhl_ShippingCore.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
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
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
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
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     */
    public static function dropLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_DHLGW_LABEL_STATUS);
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
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     */
    public static function dropDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(Constants::TABLE_DHLGW_RECIPIENT_STREET);
    }

    /**
     * Drop shipping option selection table
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
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
     * Remove Service
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function removeAdditionalFeeColumns(SchemaSetupInterface $schemaSetup)
    {
        $checkoutConnection = $schemaSetup->getConnection(Constants::CHECKOUT_CONNECTION_NAME);
        $checkoutConnection->dropColumn(
            $schemaSetup->getTable(Constants::QUOTE_TABLE_NAME, Constants::CHECKOUT_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_FIELD_NAME
        );

        $checkoutConnection->dropColumn(
            $schemaSetup->getTable(Constants::QUOTE_ADDRESS_TABLE_NAME, Constants::CHECKOUT_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_FIELD_NAME
        );

        $checkoutConnection->dropColumn(
            $schemaSetup->getTable(Constants::QUOTE_ADDRESS_TABLE_NAME, Constants::CHECKOUT_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_BASE_FIELD_NAME
        );


        $checkoutConnection->dropColumn(
            $schemaSetup->getTable(Constants::QUOTE_TABLE_NAME, Constants::CHECKOUT_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_BASE_FIELD_NAME
        );

        $salesConnection = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME);
        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::ORDER_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_FIELD_NAME
        );

        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::ORDER_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_BASE_FIELD_NAME
        );

        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::INVOICE_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_FIELD_NAME
        );

        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::INVOICE_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_BASE_FIELD_NAME
        );

        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::CREDITMEMO_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_FIELD_NAME
        );

        $salesConnection->dropColumn(
            $schemaSetup->getTable(Constants::CREDITMEMO_TABLE_NAME, Constants::SALES_CONNECTION_NAME),
            Constants::ADDITIONAL_FEE_BASE_FIELD_NAME
        );
    }
}
