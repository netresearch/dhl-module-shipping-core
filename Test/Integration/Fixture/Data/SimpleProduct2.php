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
 * Class SimpleProduct2
 *
 * @package Dhl\Test\Integration\Fixture
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
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
            DGCategory::CODE => null,
            ExportDescription::CODE => 'Export description of a second simple product.',
            TariffNumber::CODE => '876543',
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
