<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup;

use Dhl\ShippingCore\Setup\Module\Uninstaller;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Uninstall
 *
 * @package Dhl\ShippingCore\Setup
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class Uninstall implements UninstallInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * Uninstall constructor.
     * @param EavSetup $eavSetup
     */
    public function __construct(EavSetup $eavSetup)
    {
        $this->eavSetup = $eavSetup;
    }

    /**
     * Remove data that was created during module installation.
     *
     * @param SchemaSetupInterface $schemaSetup
     * @param ModuleContextInterface $context
     *
     */
    public function uninstall(SchemaSetupInterface $schemaSetup, ModuleContextInterface $context)
    {
        Uninstaller::deleteConfig($schemaSetup);
        Uninstaller::deleteAttributes($this->eavSetup);
        Uninstaller::deleteLabelStatusColumn($schemaSetup);
        Uninstaller::dropLabelStatusTable($schemaSetup);
        Uninstaller::dropDhlRecipientStreetTable($schemaSetup);
        Uninstaller::dropShippingOptionSelectionTables($schemaSetup);
    }
}
