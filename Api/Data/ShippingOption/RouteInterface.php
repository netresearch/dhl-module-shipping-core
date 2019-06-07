<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface RouteInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface RouteInterface
{
    /**
     * Get the allowed origin for a route. Will return an empty string if the route has no origin restriction.
     *
     * @return string
     */
    public function getOrigin(): string;

    /**
     * Get a list of country codes of allowed destination countries. The special "intl" code is interpreted as all
     * countries, the code "eu" is expanded to a list of countries in the EU.
     *
     * @return string[]
     */
    public function getIncludeDestinations(): array;

    /**
     * Get a list of country codes of prohibited destination countries. The special "intl" code is interpreted as all
     * countries, the code "eu" is expanded to a list of countries in the EU.
     *
     * @return string[]
     */
    public function getExcludeDestinations(): array;

    /**
     * @param string $origin
     *
     * @return void
     */
    public function setOrigin(string $origin);

    /**
     * @param string[] $includeDestinations
     *
     * @return void
     */
    public function setIncludeDestinations(array $includeDestinations);

    /**
     * @param string[] $excludeDestinations
     *
     * @return void
     */
    public function setExcludeDestinations(array $excludeDestinations);
}
