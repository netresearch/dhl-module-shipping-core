<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Dhl\ShippingCore\Model\Attribute\Migrate;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\Patch\PatchVersionInterface;

class MigrateProductAttributesPatch implements DataPatchInterface, PatchVersionInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    /**
     * @var Migrate
     */
    private $migrateAttributes;

    public function __construct(EavSetup $eavSetup, Migrate $migrateAttributes)
    {
        $this->eavSetup = $eavSetup;
        $this->migrateAttributes = $migrateAttributes;
    }

    public static function getDependencies(): array
    {
        return [
            CreateProductAttributesPatch::class,
        ];
    }

    public function getAliases(): array
    {
        return [];
    }

    /**
     * Migrate product attributes from the legacy dhl/module-shipping-m2 extension
     *
     * @return void
     */
    public function apply()
    {
        if ($this->eavSetup->getAttribute(Product::ENTITY, Migrate::DHL_DG_CATEGORY)) {
            $this->migrateAttributes->runAttributeMigration();
        }
    }

    public static function getVersion(): string
    {
        return '0.1.0';
    }
}
