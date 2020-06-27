<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\Util;

use Dhl\ShippingCore\Model\Util\StreetSplitter;
use Dhl\ShippingCore\Test\Provider\StreetDataProvider;
use PHPUnit\Framework\TestCase;

class StreetSplitterTest extends TestCase
{
    /**
     * @return string[][][]
     */
    public function getStreetData(): array
    {
        return StreetDataProvider::getStreetData();
    }

    /**
     * @dataProvider getStreetData
     *
     * @param string[] $street
     * @param string[] $expected
     */
    public function testSplitStreet(array $street, array $expected)
    {
        $splitter = new StreetSplitter();
        $street = implode(', ', $street);
        $split = $splitter->splitStreet($street);
        $this->assertEquals($expected, $split);
    }
}
