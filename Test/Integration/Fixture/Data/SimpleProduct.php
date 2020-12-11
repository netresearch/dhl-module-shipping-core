<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Catalog\Model\Product\Type;

/**
 * Regular simple product with qty=1.
 */
class SimpleProduct implements ProductInterface
{
    public function getType(): string
    {
        return Type::TYPE_SIMPLE;
    }

    public function getSku(): string
    {
        return 'DHL-01';
    }

    public function getPrice(): float
    {
        return 24.99;
    }

    public function getWeight(): float
    {
        return 2.4;
    }

    public function getCustomAttributes(): array
    {
        return [
            Constants::ATTRIBUTE_CODE_DG_CATEGORY => null,
            Constants::ATTRIBUTE_CODE_EXPORT_DESCRIPTION => 'Export description of a simple product.',
            Constants::ATTRIBUTE_CODE_TARIFF_NUMBER => '12345678',
        ];
    }

    public function getCheckoutQty(): int
    {
        return 2;
    }

    public function getDescription(): string
    {
        return 'Test Product Description';
    }
}
