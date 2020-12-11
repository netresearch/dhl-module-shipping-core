<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Setup\Patch\Data;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Catalog\Model\Product;
use Magento\Eav\Setup\EavSetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class RemoveTariffNumberBackendModelPatch implements DataPatchInterface
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

    public function apply()
    {
        $this->eavSetup->updateAttribute(
            Product::ENTITY,
            Constants::ATTRIBUTE_CODE_TARIFF_NUMBER,
            'backend_model',
            null
        );
    }
}
