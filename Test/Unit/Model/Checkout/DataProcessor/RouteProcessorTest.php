<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Model\Checkout\DataProcessor\RouteProcessor;
use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\ShippingOption\Route;
use Dhl\ShippingCore\Model\ShippingOption\ShippingOption;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RouteProcessorTest
 *
 * @package Dhl\ShippingCore\Test\Unit\Model\Checkout\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class RouteProcessorTest extends TestCase
{
    /**
     * @var Config|MockObject
     */
    private $configMock;

    protected function setUp()
    {
        parent::setUp();

        $this->configMock = $this->getMockBuilder(Config::class)
                                 ->disableOriginalConstructor()
                                 ->getMock();
        $this->configMock->method('getEuCountries')->willReturn(['de', 'at', 'it', 'uk']);
    }

    public function dataProvider(): array
    {
        return [
            'de => us, no routes specified' => [
                'optionsData' => [new ShippingOption('test')],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 1,
            ],
            'de => us, only to europe allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', ['eu'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 0,
            ],
            'de => at, only to europe allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', ['eu'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 1,
            ],
            'de => us, europe not allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', [], ['eu'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 1,
            ],
            'de => at, europe not allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', [], ['eu'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 0,
            ],
            'de => de, only domestic allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', [], ['intl'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'de',
                'expectedCount' => 1,
            ],
            'de => at, only domestic allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('', [], ['eu'])
                ])],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 0,
            ],
            'us => hk, only from de allowed' => [
                'optionsData' => [new ShippingOption('test', '', [], [
                    new Route('de', ['intl'], [])
                ])],
                'originCountryId' => 'us',
                'destinationCountryId' => 'hk',
                'expectedCount' => 0,
            ],
        ];
    }

    /**
     * @param array $optionsData
     * @param string $originCountryId
     * @param string $destinationCountryId
     * @param int $expectedCount
     * @dataProvider dataProvider
     */
    public function testProcess(
        array $optionsData,
        string $originCountryId,
        string $destinationCountryId,
        int $expectedCount
    ) {
        $this->configMock->method('getOriginCountry')->willReturn($originCountryId);
        /** @var RouteProcessor $subject */
        $subject = new RouteProcessor($this->configMock);
        $result = $subject->processShippingOptions($optionsData, $destinationCountryId, '00000');

        self::assertCount(
            $expectedCount,
            $result,
            'The route processor failed to filter the given shipping option correctly.'
        );
    }
}
