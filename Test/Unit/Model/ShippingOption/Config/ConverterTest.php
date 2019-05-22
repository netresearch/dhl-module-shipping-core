<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config;

use Dhl\ShippingCore\Model\ShippingOption\Config\Converter;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    public function xmlDataProvider(): array
    {
        $dom = new \DOMDocument();
        $dom->loadXML(
            file_get_contents(__DIR__ . '/../../../Fixture/shipping_options.xml')
        );
        return [
            'test case 1' => [
                'xml' => $dom,
            ],
        ];
    }

    /**
     * @dataProvider xmlDataProvider
     */
    public function testConvert(\DOMDocument $xml)
    {
        $subject = new Converter();

        $result = $subject->convert($xml);

        self::assertNotEmpty($result);
    }
}
