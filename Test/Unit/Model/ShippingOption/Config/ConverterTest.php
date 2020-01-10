<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\ShippingOption\Config;

use Dhl\ShippingCore\Model\ShippingSettings\Config\Converter;
use Dhl\ShippingCore\Model\Util\ConstantResolver;
use PHPUnit\Framework\TestCase;

class ConverterTest extends TestCase
{
    const CODE_PARCEL_ANNOUNCEMENT = 'parcelAnnouncement';

    /**
     * @var string[]
     */
    private $nesting = [''];

    public function xmlDataProvider(): array
    {
        $xml = new \DOMDocument();
        $xml->loadXML(
            file_get_contents(__DIR__ . '/../../../Fixture/shipping_settings.xml')
        );
        $json = \json_decode(
            file_get_contents(__DIR__ . '/../../../Fixture/shipping_settings_expected.json'),
            true
        );
        return [
            'test case 1' => [
                'xml' => $xml,
                'expectedJson' => $json
            ],
        ];
    }

    /**
     * @dataProvider xmlDataProvider
     */
    public function testConvert(\DOMDocument $xml, array $expectedJson)
    {
        $subject = new Converter(new ConstantResolver());

        $result = $subject->convert($xml);

        $this->compareRecursive($expectedJson, $result);
        $this->compareRecursive($result, $expectedJson);
        self::assertSame($expectedJson, $result);
    }

    /**
     * @param mixed|mixed[] $a
     * @param mixed|mixed[] $b
     */
    private function compareRecursive($a, $b)
    {
        foreach ($a as $aKey => $aValue) {
            self::assertArrayHasKey($aKey, $b, 'Keys don\'t match at ' . implode('/', $this->nesting));
            $bValue = $b[$aKey];
            if (is_array($bValue) && is_array($aValue)) {
                $this->nesting[] = $aKey;
                $this->compareRecursive($aValue, $bValue);
                array_pop($this->nesting);
            }
            self::assertSame($bValue, $aValue, 'Values don\'t match at ' . implode('/', $this->nesting));
        }
    }
}
