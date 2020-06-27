<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface;

class Route implements RouteInterface
{
    /**
     * @var string
     */
    private $origin = '';

    /**
     * @var string[]
     */
    private $includeDestinations = [];

    /**
     * @var string[]
     */
    private $excludeDestinations = [];

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
