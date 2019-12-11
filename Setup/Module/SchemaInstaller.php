<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Api\Data\OrderItemAttributesInterface;
use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\Module\Setup;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\Data\OrderItemInterface;

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
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function addLabelStatusColumn(SchemaSetupInterface $schemaSetup)
    {
        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->addColumn(
            $schemaSetup->getTable('sales_order_grid', Constants::SALES_CONNECTION_NAME),
            Constants::COLUMN_DHLGW_LABEL_STATUS,
            [
                'type' => Table::TYPE_TEXT,
                'length' => 10,
                'comment' => 'DHL Label Status',
            ]
        );
    }

    /**
     * Create label status table.
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createLabelStatusTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)
                             ->newTable(
                                 $schemaSetup->getTable(
                                     Constants::TABLE_LABEL_STATUS,
                                     Constants::SALES_CONNECTION_NAME
                                 )
                             );

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
                $schemaSetup->getTable(Constants::TABLE_LABEL_STATUS, Constants::SALES_CONNECTION_NAME),
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
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createDhlRecipientStreetTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(Constants::TABLE_RECIPIENT_STREET, Constants::SALES_CONNECTION_NAME)
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
                $schemaSetup->getTable(Constants::TABLE_RECIPIENT_STREET, Constants::SALES_CONNECTION_NAME),
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
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createShippingOptionSelectionTables(SchemaSetupInterface $schemaSetup)
    {
        $quoteTableName = $schemaSetup->getTable(
            Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION,
            Constants::CHECKOUT_CONNECTION_NAME
        );
        $orderTableName = $schemaSetup->getTable(
            Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION,
            Constants::SALES_CONNECTION_NAME
        );

        $quoteTable = $schemaSetup->getConnection(Constants::CHECKOUT_CONNECTION_NAME)->newTable($quoteTableName);
        $orderTable = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->newTable($orderTableName);

        /** @var Table $table */
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
                $quoteTableName,
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
                $orderTableName,
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

    /**
     * Add columns to sales documents for service charge total values
     *
     * @param SchemaSetupInterface|Setup $schemaSetup
     */
    public static function createAdditionalFeeColumns(SchemaSetupInterface $schemaSetup)
    {
        $allTables = [
            Constants::CHECKOUT_CONNECTION_NAME => ['quote'],
            Constants::SALES_CONNECTION_NAME => [
                'sales_order',
                'sales_invoice',
                'sales_creditmemo',
            ],
        ];
        $columnDefinition = [
            'type' => Table::TYPE_DECIMAL,
            'nullable' => true,
            'length' => '12,4',
            'comment' => 'DHLGW Additional Fee',
        ];
        foreach ($allTables as $connection => $tables) {
            foreach ($tables as $table) {
                $adapter = $schemaSetup->getConnection($connection);
                $adapter
                    ->addColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME,
                        $columnDefinition
                    );
                $adapter
                    ->addColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME,
                        $columnDefinition
                    );
                $adapter
                    ->addColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_FIELD_NAME,
                        $columnDefinition
                    );
                $adapter
                    ->addColumn(
                        $schemaSetup->getTable($table, $connection),
                        TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME,
                        $columnDefinition
                    );
            }
        }
    }

    /**
     * @param SchemaSetupInterface|Setup $schemaSetup
     * @throws \Zend_Db_Exception
     */
    public static function createOrderItemTable(SchemaSetupInterface $schemaSetup)
    {
        $table = $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->newTable(
            $schemaSetup->getTable(Constants::TABLE_ORDER_ITEM, Constants::SALES_CONNECTION_NAME)
        );

        $table->addColumn(
            OrderItemAttributesInterface::ITEM_ID,
            Table::TYPE_INTEGER,
            10,
            ['unsigned' => true, 'nullable' => false, 'primary' => true],
            'Order Item Id'
        );

        $table->addColumn(
            OrderItemAttributesInterface::TARIFF_NUMBER,
            Table::TYPE_TEXT,
            10,
            ['default' => null, 'nullable' => true],
            'Tariff Number (HS Code)'
        );

        $table->addColumn(
            OrderItemAttributesInterface::DG_CATEGORY,
            Table::TYPE_TEXT,
            50,
            ['default' => null, 'nullable' => true],
            'Dangerous Goods Category'
        );

        $table->addColumn(
            OrderItemAttributesInterface::EXPORT_DESCRIPTION,
            Table::TYPE_TEXT,
            50,
            ['default' => null, 'nullable' => true],
            'Export Description'
        );

        $table->addColumn(
            OrderItemAttributesInterface::COUNTRY_OF_MANUFACTURE,
            Table::TYPE_TEXT,
            2,
            ['default' => null, 'nullable' => true],
            'Country of Manufacture'
        );

        $table->addForeignKey(
            $schemaSetup->getFkName(
                $schemaSetup->getTable(Constants::TABLE_ORDER_ITEM, Constants::SALES_CONNECTION_NAME),
                OrderItemAttributesInterface::ITEM_ID,
                $schemaSetup->getTable('sales_order_item', Constants::SALES_CONNECTION_NAME),
                OrderItemInterface::ITEM_ID
            ),
            OrderItemAttributesInterface::ITEM_ID,
            $schemaSetup->getTable('sales_order_item', Constants::SALES_CONNECTION_NAME),
            OrderItemInterface::ITEM_ID,
            Table::ACTION_CASCADE
        );
        $schemaSetup->getConnection(Constants::SALES_CONNECTION_NAME)->createTable($table);
    }
}
