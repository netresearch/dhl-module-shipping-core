<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\LabelStatusManagementInterface;
use Dhl\ShippingCore\Api\RecipientStreetInterface;
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
class SchemaInstaller
{
    /**
     * Add label status column to orders grid.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     */
    public static function addLabelStatusColumn(SchemaSetupInterface $schemaSetup)
    {
        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->addColumn(
            $schemaSetup->getTable('sales_order_grid', Constants::SALES_CONNECTION_NAME),
            Constants::COLUMN_DHLGW_LABEL_STATUS,
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
     * @throws \Zend_Db_Exception
     */
    public static function createLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)
            ->newTable($schemaSetup->getTable(Constants::TABLE_DHLGW_LABEL_STATUS, Constants::SALES_CONNECTION_NAME));

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
                $schemaSetup->getTable(Constants::TABLE_DHLGW_LABEL_STATUS, Constants::SALES_CONNECTION_NAME),
                'order_id',
                $schemaSetup->getTable('sales_order', Constants::SALES_CONNECTION_NAME),
                'entity_id'
            ),
            'order_id',
            $schemaSetup->getTable('sales_order', Constants::SALES_CONNECTION_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(Constants::TABLE_DHLGW_RECIPIENT_STREET, Constants::SALES_CONNECTION_NAME)
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
                $schemaSetup->getTable(Constants::TABLE_DHLGW_RECIPIENT_STREET, Constants::SALES_CONNECTION_NAME),
                RecipientStreetInterface::ORDER_ADDRESS_ID,
                $schemaSetup->getTable('sales_order_address', Constants::SALES_CONNECTION_NAME),
                OrderAddressInterface::ENTITY_ID
            ),
            RecipientStreetInterface::ORDER_ADDRESS_ID,
            $schemaSetup->getTable('sales_order_address', Constants::SALES_CONNECTION_NAME),
            OrderAddressInterface::ENTITY_ID,
            Table::ACTION_CASCADE
        );

        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->createTable($table);
    }

    /**
     * Create Shipping Option selection tables.
     *
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createShippingOptionSelectionTables(SchemaSetupInterface $schemaSetup)
    {
        $quoteTable = $schemaSetup->getConnection(Constants::CHECKOUT_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION, Constants::CHECKOUT_CONNECTION_NAME)
        );
        $orderTable = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION, Constants::SALES_CONNECTION_NAME)
        );

        /** @var \Magento\Framework\DB\Ddl\Table $table */
        foreach ([$quoteTable, $orderTable] as $table) {
            $table->addColumn(
                'entity_id',
                Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            );
            $table->addColumn(
                AssignedSelectionInterface::PARENT_ID,
                Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Parent ID'
            );
            $table->addColumn(
                AssignedSelectionInterface::SHIPPING_OPTION_CODE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Service Code'
            );
            $table->addColumn(
                AssignedSelectionInterface::INPUT_CODE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Service Input'
            );
            $table->addColumn(
                AssignedSelectionInterface::INPUT_VALUE,
                Table::TYPE_TEXT,
                null,
                ['nullable' => false],
                'Service Value'
            );
        }

        $quoteTable->addForeignKey(
            $schemaSetup->getFkName(
                $schemaSetup->getTable(Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION, Constants::CHECKOUT_CONNECTION_NAME),
                'parent_id',
                $schemaSetup->getTable('quote_address', Constants::CHECKOUT_CONNECTION_NAME),
                'address_id'
            ),
            'parent_id',
            $schemaSetup->getTable('quote_address'),
            'address_id',
            Table::ACTION_CASCADE
        );

        $orderTable->addForeignKey(
            $schemaSetup->getFkName(
                $schemaSetup->getTable(Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION, Constants::SALES_CONNECTION_NAME),
                'parent_id',
                $schemaSetup->getTable('sales_order_address', Constants::SALES_CONNECTION_NAME),
                'entity_id'
            ),
            'parent_id',
            $schemaSetup->getTable('sales_order_address', Constants::SALES_CONNECTION_NAME),
            'entity_id',
            Table::ACTION_CASCADE
        );

        $schemaSetup->getConnection(Constants::CHECKOUT_CONNECTION_NAME)->createTable($quoteTable);
        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->createTable($orderTable);
    }
}
