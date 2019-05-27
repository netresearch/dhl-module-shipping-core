<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;
use Dhl\ShippingCore\Model\Config\CoreConfig;

/**
 * Class RouteProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class RouteProcessor extends AbstractProcessor
{
    /**
     * @var CoreConfig
     */
    private $coreConfig;

    /**
     * RouteProcessor constructor.
     *
     * @param CoreConfig $coreConfig
     */
    public function __construct(CoreConfig $coreConfig)
    {
        $this->coreConfig = $coreConfig;
    }

    /**
     * Remove all shipping options that do not match the route (origin and destination) of the current checkout.
     *
     * @param array optionsData
     * @param string $countryId     Destination country code
     * @param string $postalCode    Destination postal code
     * @param int|null $scopeId
     * @return array
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $shippingOrigin = strtolower($this->coreConfig->getOriginCountry($scopeId));
        $countryId = strtolower($countryId);

        foreach ($optionsData as $optionCode => $shippingOption) {
            $matchesRoute = $this->checkIfOptionMatchesRoute($shippingOption, $shippingOrigin, $countryId);
            if (!$matchesRoute) {
                unset($optionsData[$optionCode]);
            }
        }

        return $optionsData;
    }

    /**
     * @param array $route
     * @return array
     */
    private function preprocessDestinations(array $route): array
    {
        foreach ($route['includeDestinations'] ?? [] as $key => $destination) {
            if ($destination === 'eu') {
                unset($route['includeDestinations'][$key]);
                $route['includeDestinations'] += $this->coreConfig->getEuCountries();
            }
        }
        foreach ($route['excludeDestinations'] ?? [] as $key => $destination) {
            if ($destination === 'eu') {
                unset($route['excludeDestinations'][$key]);
                $route['excludeDestinations'] += $this->coreConfig->getEuCountries();
            }
        }
        return $route;
    }

    /**
     * @param $shippingOption
     * @param $shippingOrigin
     * @param string $countryId
     * @return bool
     */
    private function checkIfOptionMatchesRoute($shippingOption, $shippingOrigin, string $countryId): bool
    {
        if (!isset($shippingOption['routes'])) {
            // Option matches all routes
            return true;
        }
        $matchingRoutes = array_filter(
            $shippingOption['routes'],
            function ($route) use ($shippingOrigin, $countryId) {
                $route = $this->preprocessDestinations($route);
                return $this->isRouteAllowed($route, $shippingOrigin, $countryId);
            }
        );

        return !empty($matchingRoutes);
    }

    /**
     * @param mixed[] $route
     * @param string $origin
     * @param string $destination
     * @return bool
     */
    private function isRouteAllowed(array $route, string $origin, string $destination): bool
    {
        if (isset($route['origin']) && $route['origin'] !== $origin) {
            return false;
        }

        $includeDestinations = $route['includeDestinations'] ?? ['intl'];
        $excludeDestinations = $route['excludeDestinations'] ?? [];
        $hasIncludes = !in_array('intl', $includeDestinations, true);
        $hasExcludes = !empty($excludeDestinations);

        if (!$hasIncludes && !$hasExcludes) {
            return true;
        }

        if ($hasIncludes && !in_array($destination, $includeDestinations, true)) {
            return false;
        }

        if (in_array('intl', $excludeDestinations, true)) {
            return $origin === $destination;
        }

        if ($hasExcludes && in_array($destination, $excludeDestinations, true)) {
            return false;
        }

        return true;
    }
}
