<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Dhl\ShippingCore\Setup\Module\DataInstaller;
use Dhl\ShippingCore\Setup\Module\Uninstaller;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchRevertableInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class CreateProductAttributesPatch implements PatchRevertableInterface, DataPatchInterface, PatchVersionInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @var EavSetupFactory
     */
    private $eavSetupFactory;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    public static function getDependencies(): array
    {
        return [];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * Create product attributes for customs relevant data
     *
     * @return void
     */
    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        DataInstaller::addDangerousGoodsCategoryAttribute($eavSetup);
        DataInstaller::addTariffNumberAttribute($eavSetup);
        DataInstaller::addExportDescriptionAttribute($eavSetup);
    }

    public function revert()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);
        Uninstaller::deleteAttributes($eavSetup);
    }

    public static function getVersion(): string
    {
        return '0.1.0';
    }
}
