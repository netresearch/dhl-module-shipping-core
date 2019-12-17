<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Unit\Model\Checkout\DataProcessor\ServiceOptions;

use Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\ServiceOptions\RouteProcessor;
use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\ShippingSettings\RouteMatcher;
use Dhl\ShippingCore\Model\ShippingSettings\Data\Route;
use Dhl\ShippingCore\Model\ShippingSettings\Data\ShippingOption;
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
        $this->configMock->method('getEuCountries')->willReturn(['DE', 'AT', 'IT', 'UK']);
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
        $routeNonEu->setOrigin('eu');
        $routeNonEu->setExcludeDestinations(['eu']);
        $optionDestinationNonEu = new ShippingOption();
        $optionDestinationNonEu->setCode('test');
        $optionDestinationNonEu->setRoutes([$routeNonEu]);

        $routeNonIntl = new Route();
        $routeNonIntl->setIncludeDestinations(['domestic']);
        $optionDestinationNonIntl = new ShippingOption();
        $optionDestinationNonIntl->setCode('test');
        $optionDestinationNonIntl->setRoutes([$routeNonIntl]);

        $routeDeToIntl = new Route();
        $routeDeToIntl->setOrigin('de');
        $routeDeToIntl->setExcludeDestinations(['domestic']);
        $optionDeToIntl = new ShippingOption();
        $optionDeToIntl->setCode('test');

        return [
            'de => us, no routes specified' => [
                'optionsData' => [$optionNoRoute],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 1,
            ],
            'de => us, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 0,
            ],
            'de => at, eu destination required' => [
                'optionsData' => [$optionDestinationEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 1,
            ],
            'de => us, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'US',
                'expectedCount' => 1,
            ],
            'de => at, eu destination not allowed' => [
                'optionsData' => [$optionDestinationNonEu],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 0,
            ],
            'de => de, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'DE',
                'expectedCount' => 1,
            ],
            'de => at, only domestic allowed' => [
                'optionsData' => [$optionDestinationNonIntl],
                'originCountryId' => 'DE',
                'destinationCountryId' => 'AT',
                'expectedCount' => 0,
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
        $routeMatcher = new RouteMatcher($this->configMock);
        /** @var RouteProcessor $subject */
        $subject = new RouteProcessor($this->configMock, $routeMatcher);
        $result = $subject->process($optionsData, $destinationCountryId, '00000', 0);

        self::assertCount(
            $expectedCount,
            $result,
            'The route processor failed to filter the given shipping option correctly.'
        );
    }
}
