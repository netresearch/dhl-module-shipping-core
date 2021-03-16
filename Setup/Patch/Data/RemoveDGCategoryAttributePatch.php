<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveDGCategoryAttributePatch implements DataPatchInterface
{
    /**
     * @var EavSetup
     */
    private $eavSetup;

    public function __construct(EavSetup $eavSetup)
    {
        $this->eavSetup = $eavSetup;
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
     * Clean up the `dhlgw_dangerous_goods_category` product which was never used for anything.
     *
     * @return void
     * @throws \Exception
     */
    public function apply()
    {
        if (!$this->eavSetup->getAttribute(Product::ENTITY, 'dhlgw_dangerous_goods_category')) {
            return;
        }

        $this->eavSetup->removeAttribute(Product::ENTITY, 'dhlgw_dangerous_goods_category');
    }
}
