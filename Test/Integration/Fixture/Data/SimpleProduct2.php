<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Catalog\Model\Product\Type;

/**
 * Regular simple product with qty=2.
 */
class SimpleProduct2 implements ProductInterface
{
    public function getType(): string
    {
        return Type::TYPE_SIMPLE;
    }

    public function getSku(): string
    {
        return 'DHL-02';
    }

    public function getPrice(): float
    {
        return 14.99;
    }

    public function getWeight(): float
    {
        return 1.1;
    }

    public function getCustomAttributes(): array
    {
        return [
            Constants::ATTRIBUTE_CODE_DG_CATEGORY => null,
            Constants::ATTRIBUTE_CODE_EXPORT_DESCRIPTION => 'Export description of a second simple product.',
            Constants::ATTRIBUTE_CODE_TARIFF_NUMBER => '876543',
        ];
    }

    public function getCheckoutQty(): int
    {
        return 1;
    }

    public function getDescription(): string
    {
        return 'Test Product 2 Description';
    }
}
