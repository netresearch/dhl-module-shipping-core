<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\Checkout\DataProcessor\ServiceOptions;

use Dhl\ShippingCore\Model\Checkout\DataProcessor\ServiceOptions\RouteProcessor;
use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\ShippingOption\Route;
use Dhl\ShippingCore\Model\ShippingOption\ShippingOption;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Class RouteProcessorTest
 *
 * @package Dhl\ShippingCore\Test\Unit
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
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
        $optionNoRoute = new ShippingOption();
        $optionNoRoute->setCode('test');

        $routeEu = new Route();
        $routeEu->setIncludeDestinations(['eu']);
        $optionDestinationEu = new ShippingOption();
        $optionDestinationEu->setCode('test');
        $optionDestinationEu->setRoutes([$routeEu]);

        $routeNonEu = new Route();
        $routeNonEu->setExcludeDestinations(['eu']);
        $optionDestinationNonEu = new ShippingOption();
        $optionDestinationNonEu->setCode('test');
        $optionDestinationNonEu->setRoutes([$routeNonEu]);

        $routeNonIntl = new Route();
        $routeNonIntl->setExcludeDestinations(['intl']);
        $optionDestinationNonIntl = new ShippingOption();
        $optionDestinationNonIntl->setCode('test');
        $optionDestinationNonIntl->setRoutes([$routeNonIntl]);

        $routeDeToIntl = new Route();
        $routeDeToIntl->setOrigin('de');
        $routeDeToIntl->setIncludeDestinations(['intl']);
        $optionDeToIntl = new ShippingOption();
        $optionDeToIntl->setCode('test');
        $optionDeToIntl->setRoutes([$routeDeToIntl]);

        return [
            'de => us, no routes specified' => [
                'optionsData' => [$optionNoRoute],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 1,
            ],
            'de => us, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 0,
            ],
            'de => at, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 1,
            ],
            'de => us, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'de',
                'destinationCountryId' => 'us',
                'expectedCount' => 1,
            ],
            'de => at, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 0,
            ],
            'de => de, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'de',
                'destinationCountryId' => 'de',
                'expectedCount' => 1,
            ],
            'de => at, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'de',
                'destinationCountryId' => 'at',
                'expectedCount' => 0,
            ],
            'de => hk, all destinations from de allowed' => [
                'optionsData' => [$optionDeToIntl],
                'originCountryId' => 'de',
                'destinationCountryId' => 'hk',
                'expectedCount' => 1,
            ],
            'us => hk, all destinations from de allowed' => [
                'optionsData' => [$optionDeToIntl],
                'originCountryId' => 'us',
                'destinationCountryId' => 'hk',
                'expectedCount' => 0,
            ],
            'de => de, all destinations from de allowed' => [
                'optionsData' => [$optionDeToIntl],
                'originCountryId' => 'de',
                'destinationCountryId' => 'de',
                'expectedCount' => 1,
            ],
        ];
    }

    /**
     * @dataProvider dataProvider
     *
     * @param mixed[] $optionsData
     * @param string $originCountryId
     * @param string $destinationCountryId
     * @param int $expectedCount
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
        $result = $subject->process($optionsData, $destinationCountryId, '00000');

        self::assertCount(
            $expectedCount,
            $result,
            'The route processor failed to filter the given shipping option correctly.'
        );
    }
}
