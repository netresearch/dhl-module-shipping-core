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
        '----Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST-CONST',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST-',
        'CODE_PARCEL_ANNOUNCEMENT',
        'My:test',
        'this\is\a\test',
        'Dhl_ShippingCore::images/logo-dhl.svg',
        'Parcel Announcement',
        'checkbox',
    ];

    private $brokenTestLines = [
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConverterTest::NONEXISTANT_CONSTANT',
        'Dhl\Nonexistant\Class::CODE-PARCEL-ANNOUNCEMENT',
    ];

    private $combinedTestLines = [
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST.enabled',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST.2cool4school',
        'Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST.Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config\ConstantResolverTest::TEST_CONST',
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
            self::assertSame(
                $subject->resolve($line),
                $line,
                "Constant Resolver wrongly detects '$line' as a constant."
            );
        }

        foreach ($this->combinedTestLines as $line) {
            $result = $subject->resolve($line);
            self::assertNotFalse(strpos($result, self::TEST_CONST));
            self::assertNotSame($line, $result);
        }

        $this->expectException(\RuntimeException::class);
        foreach ($this->brokenTestLines as $line) {
            $subject->resolve($line);
        }
    }
}
