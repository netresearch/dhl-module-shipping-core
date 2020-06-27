<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Provider;

use Dhl\ShippingCore\Model\ShippingSettings\Data\Route;

class RouteProvider
{
    /**
     * @return array
     */
    public static function getRoutes(): array
    {
        $includeDomestic = self::createRoute('', ['domestic'], []);
        $includeEu = self::createRoute('', ['eu'], []);
        $excludeNL = self::createRoute('', [], ['NL']);
        $excludeEu = self::createRoute('', [], ['eu']);
        $excludeDomestic = self::createRoute('', [], ['domestic']);
        $dkOriginExlcudeFr = self::createRoute('DK', [], ['FR']);
        $beOriginIncludeEu = self::createRoute('BE', ['eu'], []);
        $euOriginExcludeEu = self::createRoute('eu', [], ['eu']);
        $deOriginIncludeDeAt = self::createRoute('DE', ['DE', 'AT'], []);

        return [
            'DE => DE, no origin specified' => [
                'routes' => [
                    $includeDomestic,
                ],
                'shippingOrigin' => 'DE',
                'destination' => 'DE',
                'storeId' => 1,
                'expected' => true,
            ],
            'DE => NL, no origin specified' => [
                'routes' => [
                    $excludeNL,
                ],
                'shippingOrigin' => 'DE',
                'destination' => 'NL',
                'storeId' => 0,
                'expected' => false,
            ],
            'DE => FR, no origin specified' => [
                'routes' => [
                    $includeEu,
                ],
                'shippingOrigin' => 'DE',
                'destination' => 'FR',
                'storeId' => 0,
                'expected' => true,
            ],
            'DK => PL, no origin specified' => [
                'routes' => [
                    $excludeEu,
                ],
                'shippingOrigin' => 'DK',
                'destination' => 'PL',
                'storeId' => 0,
                'expected' => false,
            ],
            'DK => FR' => [
                'routes' => [
                    $includeDomestic,
                    $dkOriginExlcudeFr,
                ],
                'shippingOrigin' => 'DK',
                'destination' => 'FR',
                'storeId' => 0,
                'expected' => false,
            ],
            'BE => FR' => [
                'routes' => [
                    $beOriginIncludeEu,
                ],
                'shippingOrigin' => 'BE',
                'destination' => 'FR',
                'storeId' => 0,
                'expected' => true,
            ],
            'DE => IT' => [
                'routes' => [
                    $excludeDomestic,
                    $euOriginExcludeEu,
                ],
                'shippingOrigin' => 'DE',
                'destination' => 'IT',
                'storeId' => 0,
                'expected' => false,
            ],
            'US => DE' => [
                'routes' => [
                    $excludeDomestic,
                    $euOriginExcludeEu,
                ],
                'shippingOrigin' => 'US',
                'destination' => 'DE',
                'storeId' => 0,
                'expected' => true,
            ],
            'US => FR' => [
                'routes' => [
                    $includeDomestic,
                    $euOriginExcludeEu,
                ],
                'shippingOrigin' => 'US',
                'destination' => 'FR',
                'storeId' => 0,
                'expected' => false,
            ],
            'US => DE, route origin DE' => [
                'routes' => [
                    $deOriginIncludeDeAt,
                ],
                'shippingOrigin' => 'US',
                'destination' => 'AT',
                'storeId' => 0,
                'expected' => false,
            ],
        ];
    }

    /**
     * @param string $routeOrigin
     * @param string[] $includes
     * @param string[] $excludes
     * @return Route
     */
    public static function createRoute(string $routeOrigin, array $includes, array $excludes)
    {
        $route = new Route();
        $route->setOrigin($routeOrigin);
        $route->setExcludeDestinations($excludes);
        $route->setIncludeDestinations($includes);

        return $route;
    }
}
