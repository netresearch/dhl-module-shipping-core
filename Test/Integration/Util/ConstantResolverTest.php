<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config;

use Dhl\ShippingCore\Model\Util\ConstantResolver;
use PHPUnit\Framework\TestCase;

class ConstantResolverTest extends TestCase
{
    const TEST_CONST = 'test123';

    private $positiveTestLines = [
        '\Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST',
    ];
    private $negativeTestLines = [
        '\Some\Class::NONEXISTANT_CONSTANT',
        '----\Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConverterTest::CODE_PARCEL_ANNOUNCEMENT',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConverterTest::CODE-PARCEL-ANNOUNCEMENT',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConverterTest::CODE-PARCEL-ANNOUNCEMENT-',
        'CODE_PARCEL_ANNOUNCEMENT',
        'My:test',
        'this\is\a\test',
        'Dhl_Ui::images/logo-dhl-wide.svg',
        'Parcel Announcement',
        'checkbox',
    ];

    public function testResolve()
    {
        $subject = new ConstantResolver();

        foreach ($this->positiveTestLines as $line) {
            self::assertSame(
                $subject->resolve($line),
                self::TEST_CONST,
                "preg_match does not detect '$line' as a constant."
            );
        }
        foreach ($this->negativeTestLines as $line) {
            self::assertFalse($subject->resolve($line), "Constant Resolver wrongly detects '$line' as a constant.");
        }
    }
}
