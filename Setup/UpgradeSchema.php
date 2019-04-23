<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup;

use Dhl\ShippingCore\Setup\Module\SchemaInstaller;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UpgradeSchemaInterface;

/**
 * UpgradeSchema
 *
 * @package Dhl\ShippingCore\Setup
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * Upgrade DB schema for Dhl_ShippingCore.
     *
     * @param SchemaSetupInterface $schemaSetup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $schemaSetup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            SchemaInstaller::addLabelStatusColumn($schemaSetup);
            SchemaInstaller::createLabelStatusTable($schemaSetup);
            SchemaInstaller::createDhlRecipientStreetTable($schemaSetup);
            SchemaInstaller::createShippingOptionSelectionTables($schemaSetup);
        }
    }
}
