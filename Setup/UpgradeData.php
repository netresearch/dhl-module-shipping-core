<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Model\Attribute\Migrate;
use Dhl\ShippingCore\Setup\DataInstaller;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class UpgradeData
 *
 * @package Dhl\ShippingCore\Setup
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    /**
     * @var Migrate
     */
    private $migrateAttributes;

    /**
     * UpgradeData constructor.
     *
     * @param EavSetupFactory $eavSetupFactory
     * @param Migrate $migrate
     */
    public function __construct(EavSetupFactory $eavSetupFactory, Migrate $migrate)
    {
        $this->eavSetupFactory = $eavSetupFactory;
        $this->migrateAttributes = $migrate;
    }

    /**
     * Upgrade DB data for Dhl_ShippingCore.
     *
     * @param ModuleDataSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), '0.1.0', '<')) {
            DataInstaller::addDangerousGoodsCategoryAttribute($eavSetup);
            DataInstaller::addTariffNumberAttribute($eavSetup);
            DataInstaller::addExportDescriptionAttribute($eavSetup);

            if ($eavSetup->getAttribute(Product::ENTITY, Migrate::DHL_DG_CATEGORY)) {
                $this->migrateAttributes->runAttributeMigration();
            }
        }
    }
}
