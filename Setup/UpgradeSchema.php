<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup;

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
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $schemaSetup
     * @param ModuleContextInterface $context
     * @throws \Zend_Db_Exception
     */
    public function upgrade(SchemaSetupInterface $schemaSetup, ModuleContextInterface $context)
    {
        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            Setup::addLabelStatusColumn($schemaSetup);
            Setup::createLabelStatusTable($schemaSetup);
        }
    }
}
