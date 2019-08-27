<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Util;

use Dhl\ShippingCore\Test\Provider\StreetDataProvider;
use Dhl\ShippingCore\Util\StreetSplitter;
use PHPUnit\Framework\TestCase;

/**
 * Class StreetSplitterTest
 *
 * @package Dhl\ShippingCore\Test\Unit
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
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
