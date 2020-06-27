<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Module;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface;
use Magento\Eav\Setup\EavSetup;

class DataInstaller
{
    /**
     * @param EavSetup $eavSetup
     */
    public static function addDangerousGoodsCategoryAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            DGCategory::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'DHL Dangerous Goods Category',
                'input' => 'select',
                'required' => false,
                'source' => DGCategory::class,
                'sort_order' => 50,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
                'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE]),
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    public static function addTariffNumberAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            TariffNumber::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'DHL Tariff Number (HS Code)',
                'frontend_class' => 'validate-digits validate-length maximum-length-11',
                'input' => 'text',
                'required' => false,
                'sort_order' => 51,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'backend' => TariffNumber::class,
                'visible' => true,
                'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE]),
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    public static function addExportDescriptionAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            Product::ENTITY,
            ExportDescription::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'DHL Item Description',
                'frontend_class' => 'validate-length maximum-length-50',
                'input' => 'text',
                'required' => false,
                'sort_order' => 52,
                'global' => ScopedAttributeInterface::SCOPE_WEBSITE,
                'backend' => ExportDescription::class,
                'visible' => true,
                'apply_to' => implode(',', [Type::TYPE_SIMPLE, Type::TYPE_BUNDLE, Configurable::TYPE_CODE]),
            ]
        );
    }
}
