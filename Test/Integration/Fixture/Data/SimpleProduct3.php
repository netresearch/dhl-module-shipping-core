<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Catalog\Model\Product\Type;

/**
 * Simple product with excessive weight.
 */
class SimpleProduct3 implements ProductInterface
{
    public function getType(): string
    {
        return Type::TYPE_SIMPLE;
    }

    public function getSku(): string
    {
        return 'DHL-03';
    }

    public function getPrice(): float
    {
        return 103.99;
    }

    public function getWeight(): float
    {
        return 33.303;
    }

    public function getCustomAttributes(): array
    {
        return [
            Constants::ATTRIBUTE_CODE_DG_CATEGORY => null,
            Constants::ATTRIBUTE_CODE_EXPORT_DESCRIPTION => 'Export description of a third simple product.',
            Constants::ATTRIBUTE_CODE_TARIFF_NUMBER => '876543',
        ];
    }

    public function getCheckoutQty(): int
    {
        return 3;
    }

    public function getDescription(): string
    {
        return 'Test Product 3 Description';
    }
}
