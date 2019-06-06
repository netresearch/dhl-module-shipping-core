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
     * @return string
     */
    public function getOrigin(): string;

    /**
     * @return string[]
     */
    public function getIncludeDestinations(): array;

    /**
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
