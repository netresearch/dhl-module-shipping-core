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

/**
 * Class ShippingSetup
 *
 * @package Dhl\ShippingCore\Setup
 */
class SetupData
{
    /**
     * @param EavSetup $eavSetup
     */
    public static function addDangerousGoodsCategoryAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            DGCategory::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'Dangerous Goods Category',
                'input' => 'select',
                'required' => false,
                'source' => DGCategory::class,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'visible' => true,
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    public static function addTariffNumberAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            TariffNumber::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'Tariff Number (hs Code)',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'backend' => TariffNumber::class,
                'visible' => true,
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     */
    public static function addExportDescriptionAttribute(EavSetup $eavSetup)
    {
        $eavSetup->addAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            ExportDescription::CODE,
            [
                'group' => '',
                'type' => 'varchar',
                'label' => 'DHL Item Description',
                'input' => 'text',
                'required' => false,
                'sort_order' => 50,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_WEBSITE,
                'backend' => ExportDescription::class,
                'visible' => true,
            ]
        );
    }

    /**
     * @param EavSetup $eavSetup
     * @return void
     */
    public static function deleteAttributes(EavSetup $eavSetup)
    {
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            DGCategory::CODE
        );
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            TariffNumber::CODE
        );
        $eavSetup->removeAttribute(
            \Magento\Catalog\Model\Product::ENTITY,
            ExportDescription::CODE
        );
    }
}
