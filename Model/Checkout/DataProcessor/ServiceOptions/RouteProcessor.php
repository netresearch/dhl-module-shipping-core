<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor\ServiceOptions;

use Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\ShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Config\Config;
use Dhl\ShippingCore\Model\RouteMatcher;

/**
 * Class RouteProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class RouteProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var RouteMatcher
     */
    private $routeMatcher;

    /**
     * RouteProcessor constructor.
     *
     * @param Config $config
     * @param RouteMatcher $routeValidator
     */
    public function __construct(Config $config, RouteMatcher $routeValidator)
    {
        $this->config = $config;
        $this->routeMatcher = $routeValidator;
    }

    /**
     * Remove all shipping options that do not match the route (origin and destination) of the current checkout.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $shippingOrigin = $this->config->getOriginCountry($scopeId);

        foreach ($optionsData as $index => $shippingOption) {
            $matchesRoute = $this->routeMatcher->match(
                $shippingOption->getRoutes(),
                $shippingOrigin,
                $countryId,
                $scopeId
            );

            if (!$matchesRoute) {
                unset($optionsData[$index]);
            }
        }

        return $optionsData;
    }
}
