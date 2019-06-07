<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface;

/**
 * Class Route
 *
 * @package Dhl\ShippingCore\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $origin;

    /**
     * @var string[]
     */
    private $includeDestinations;

    /**
     * @var string[]
     */
    private $excludeDestinations;

    /**
     * Route constructor.
     *
     * @param string $origin
     * @param string[] $includeDestinations
     * @param string[] $excludeDestinations
     */
    public function __construct(string $origin = '', array $includeDestinations = [], array $excludeDestinations = [])
    {
        $this->origin = $origin;
        $this->includeDestinations = $includeDestinations;
        $this->excludeDestinations = $excludeDestinations;
    }

    /**
     * @return string
     */
    public function getOrigin(): string
    {
        return $this->origin;
    }

    /**
     * @param string $origin
     */
    public function setOrigin(string $origin)
    {
        $this->origin = $origin;
    }

    /**
     * @return string[]
     */
    public function getIncludeDestinations(): array
    {
        return $this->includeDestinations;
    }

    /**
     * @param string[] $includeDestinations
     */
    public function setIncludeDestinations(array $includeDestinations)
    {
        $this->includeDestinations = $includeDestinations;
    }

    /**
     * @return string[]
     */
    public function getExcludeDestinations(): array
    {
        return $this->excludeDestinations;
    }

    /**
     * @param string[] $excludeDestinations
     */
    public function setExcludeDestinations(array $excludeDestinations)
    {
        $this->excludeDestinations = $excludeDestinations;
    }
}
