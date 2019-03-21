<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Setup\UninstallInterface;

/**
 * Uninstall
 *
 * @package Dhl\ShippingCore\Setup
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link http://www.netresearch.de/
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
     * @param SchemaSetupInterface|\Magento\Framework\Module\Setup $setup
     * @param ModuleContextInterface $context
     *
     * @return void
     */
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $defaultConnection = $setup->getConnection(ResourceConnection::DEFAULT_CONNECTION);
        $configTable = $setup->getTable('core_config_data');
        $defaultConnection->delete($configTable, "`path` LIKE 'shipping/dhlglobalwebservices/%'");
        $this->deleteAttributes($this->eavSetup);
    }

    /**
     * @param EavSetup $uninstaller
     * @return void
     */
    private function deleteAttributes(EavSetup $uninstaller)
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
}
