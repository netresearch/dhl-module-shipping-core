<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Magento\Catalog\Model\Product\Type;

/**
 * Simple product with excessive weight.
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
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
            DGCategory::CODE => null,
            ExportDescription::CODE => 'Export description of a third simple product.',
            TariffNumber::CODE => '876543',
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
