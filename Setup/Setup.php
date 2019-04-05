<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup;

use Dhl\ShippingCore\Api\LabelStatusManagementInterface;
use Dhl\ShippingCore\Api\RecipientStreetInterface;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * Setup
 *
 * @package Dhl\ShippingCore\Setup
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class Setup
{
    const SALES_CONNECTION_NAME = 'sales';

    const TABLE_LABEL_STATUS = 'dhl_label_status';

    const DHL_RECIPIENT_STREET_TABLE_NAME ='dhl_recipient_street';

    const LABEL_STATUS_COLUMN_NAME = 'dhl_label_status';

    /**
     * Add label status column to orders grid.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function addLabelStatusColumn(SchemaSetupInterface $schemaSetup)
    {
        $schemaSetup->getConnection(self::SALES_CONNECTION_NAME)->addColumn(
            $schemaSetup->getTable('sales_order_grid', self::SALES_CONNECTION_NAME),
            self::LABEL_STATUS_COLUMN_NAME,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 10,
                'comment' => 'DHL Label Status'
            ]
        );
    }

    /**
     * Create label status table.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     * @throws \Zend_Db_Exception
     */
    public static function createLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(self::SALES_CONNECTION_NAME)
            ->newTable($schemaSetup->getTable(self::TABLE_LABEL_STATUS, self::SALES_CONNECTION_NAME));

        $table->addColumn(
            'order_id',
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            'status_code',
            Table::TYPE_TEXT,
            10,
            ['default' => LabelStatusManagementInterface::LABEL_STATUS_PENDING, 'nullable' => false],
            'Status Code'
        );

        $table->addForeignKey(
            $schemaSetup->getFkName(
                $schemaSetup->getTable(self::TABLE_LABEL_STATUS, self::SALES_CONNECTION_NAME),
                'order_id',
                $schemaSetup->getTable('sales_order', self::SALES_CONNECTION_NAME),
                'entity_id'
            ),
            'order_id',
            $schemaSetup->getTable('sales_order', self::SALES_CONNECTION_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $schemaSetup->getConnection(self::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(self::SALES_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(self::DHL_RECIPIENT_STREET_TABLE_NAME, self::SALES_CONNECTION_NAME)
        );

        $table->addColumn(
            RecipientStreetInterface::ORDER_ADDRESS_ID,
            Table::TYPE_INTEGER,
            null,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Entity Id'
        );

        $table->addColumn(
            RecipientStreetInterface::NAME,
            Table::TYPE_TEXT,
            255,
            ['default' => null, 'nullable' => true],
            'Street'
        );

        $table->addColumn(
            RecipientStreetInterface::NUMBER,
            Table::TYPE_TEXT,
            50,
            ['default' => null, 'nullable' => true],
            'Number'
        );

        $table->addColumn(
            RecipientStreetInterface::SUPPLEMENT,
            Table::TYPE_TEXT,
            100,
            ['default' => null, 'nullable' => true],
            'Supplement'
        );

        $table->addForeignKey(
            $schemaSetup->getFkName(
                $schemaSetup->getTable(self::DHL_RECIPIENT_STREET_TABLE_NAME, self::SALES_CONNECTION_NAME),
                RecipientStreetInterface::ORDER_ADDRESS_ID,
                $schemaSetup->getTable('sales_order_address', self::SALES_CONNECTION_NAME),
                OrderAddressInterface::ENTITY_ID
            ),
            RecipientStreetInterface::ORDER_ADDRESS_ID,
            $schemaSetup->getTable('sales_order_address', self::SALES_CONNECTION_NAME),
            OrderAddressInterface::ENTITY_ID,
            Table::ACTION_CASCADE
        );

        $schemaSetup->getConnection(self::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * Delete all config data related to Dhl_ShippingCore.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function deleteConfig(SchemaSetupInterface $schemaSetup)
    {
        $defaultConnection = $schemaSetup->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $configTable = $schemaSetup->getTable('core_config_data', ResourceConnection::DEFAULT_CONNECTION);
        $defaultConnection->delete($configTable, "`path` LIKE 'shipping/dhlglobalwebservices/%'");
    }

    /**
     * Remove EAV product attributes.
     *
     * @param EavSetup $uninstaller
     * @return void
     */
    public static function removeProductAttributes(EavSetup $uninstaller)
    {
        $uninstaller->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            DGCategory::CODE
        );
        $uninstaller->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            TariffNumber::CODE
        );
        $uninstaller->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            ExportDescription::CODE
        );
    }

    /**
     * Remove label status column from orders grid.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function deleteLabelStatusColumn(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(self::SALES_CONNECTION_NAME);
        $salesConnection->dropColumn(
            $schemaSetup->getTable('sales_order_grid', self::SALES_CONNECTION_NAME),
            Setup::LABEL_STATUS_COLUMN_NAME
        );
    }

    /**
     * Drop label status table.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function dropLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(self::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(self::TABLE_LABEL_STATUS);
    }

    /**
     * Drop label status table.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @return void
     */
    public static function dropDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $salesConnection = $schemaSetup->getConnection(self::SALES_CONNECTION_NAME);
        $salesConnection->dropTable(self::DHL_RECIPIENT_STREET_TABLE_NAME);
    }
}
