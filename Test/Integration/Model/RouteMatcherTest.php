<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Model;

use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\ShippingSettings\RouteMatcher;
use Dhl\ShippingCore\Test\Provider\RouteProvider;
use Magento\TestFramework\Helper\Bootstrap;
use Magento\TestFramework\ObjectManager;
use PHPUnit\Framework\TestCase;

class RouteMatcherTest extends TestCase
{
    /**
     * @var ObjectManager
     */
    private $objectManger;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var RouteMatcher
     */
    private $routeMatcher;

    protected function setUp()
    {
        parent::setUp();
        $this->objectManger = Bootstrap::getObjectManager();
        $this->config = $this->objectManger->create(Config::class);
        $this->routeMatcher = $this->objectManger->create(RouteMatcher::class, [$this->config]);
    }

    public function getRouteData(): array
    {
        return RouteProvider::getRoutes();
    }

    /**
     * @dataProvider getRouteData
     *
     * @param array $routes
     * @param string $shippingOrigin
     * @param string $destination
     * @param int $storeId
     * @param bool $expected
     */
    public function testMatch(
        $routes,
        $shippingOrigin,
        $destination,
        $storeId,
        $expected
    ) {
        $result = $this->routeMatcher->match($routes, $shippingOrigin, $destination, $storeId);
        $this->assertSame($expected, $result);
    }
}
