<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;
use Dhl\ShippingCore\Model\Config\Config;

/**
 * Class RouteProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class RouteProcessor extends AbstractProcessor
{
    /**
     * @var Config
     */
    private $config;

    /**
     * RouteProcessor constructor.
     *
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * Remove all shipping options that do not match the route (origin and destination) of the current checkout.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryId     Destination country code
     * @param string $postalCode    Destination postal code
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $shippingOrigin = strtolower($this->config->getOriginCountry($scopeId));
        $countryId = strtolower($countryId);

        foreach ($optionsData as $index => $shippingOption) {
            $matchesRoute = $this->checkIfOptionMatchesRoute($shippingOption, $shippingOrigin, $countryId);
            if (!$matchesRoute) {
                unset($optionsData[$index]);
            }
        }

        return $optionsData;
    }

    /**
     * @param ShippingOptionInterface $shippingOption
     * @param string $shippingOrigin
     * @param string $countryId
     *
     * @return bool
     */
    private function checkIfOptionMatchesRoute(
        ShippingOptionInterface $shippingOption,
        string $shippingOrigin,
        string $countryId
    ): bool {
        if (empty($shippingOption->getRoutes())) {
            // Option matches all routes
            return true;
        }
        $matchingRoutes = array_filter(
            $shippingOption->getRoutes(),
            function (RouteInterface $route) use ($shippingOrigin, $countryId) {
                $route = $this->preprocessDestinations($route);
                return $this->isRouteAllowed($route, $shippingOrigin, $countryId);
            }
        );

        return !empty($matchingRoutes);
    }

    /**
     * @param RouteInterface $route
     *
     * @return RouteInterface
     */
    private function preprocessDestinations(RouteInterface $route): RouteInterface
    {
        $includeDestinations = $route->getIncludeDestinations();
        foreach ($includeDestinations as $index => $destination) {
            if ($destination === 'eu') {
                unset($includeDestinations[$index]);
                $route->setIncludeDestinations(
                    $includeDestinations + $this->config->getEuCountries()
                );
            }
        }
        $excludeDestinations = $route->getExcludeDestinations();
        foreach ($excludeDestinations as $index => $destination) {
            if ($destination === 'eu') {
                unset($excludeDestinations[$index]);
                $route->setExcludeDestinations(
                    $excludeDestinations + $this->config->getEuCountries()
                );
            }
        }
        return $route;
    }

    /**
     * @param RouteInterface $route
     * @param string $origin
     * @param string $destination
     *
     * @return bool
     */
    private function isRouteAllowed(RouteInterface $route, string $origin, string $destination): bool
    {
        if ($route->getOrigin() && $route->getOrigin() !== $origin) {
            return false;
        }

        $includeDestinations = $route->getIncludeDestinations() ?: ['intl'];
        $excludeDestinations = $route->getExcludeDestinations();
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
