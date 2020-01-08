<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;

/**
 * Class ShippingOption
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ShippingOption implements ShippingOptionInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $available = '1';

    /**
     * @var string
     */
    private $label = '';

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    private $inputs = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    private $routes = [];

    /**
     * @var int
     */
    private $sortOrder = 0;

    /**
     * @var int[]
     */
    private $requiredItemIds = [];

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getAvailable(): string
    {
        return $this->available;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[]
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[]
     */
    public function getRoutes(): array
    {
        return $this->routes;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return int[]
     */
    public function getRequiredItemIds(): array
    {
        return $this->requiredItemIds;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @param string $available
     */
    public function setAvailable(string $available)
    {
        $this->available = $available;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface[] $inputs
     */
    public function setInputs(array $inputs)
    {
        $this->inputs = $inputs;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\RouteInterface[] $routes
     */
    public function setRoutes(array $routes)
    {
        $this->routes = $routes;
    }

    /**
     * @param int $sortOrder
     */
    public function setSortOrder(int $sortOrder)
    {
        $this->sortOrder = $sortOrder;
    }

    /**
     * @param int[] $requiredItemIds
     */
    public function setRequiredItemIds(array $requiredItemIds)
    {
        $this->requiredItemIds = $requiredItemIds;
    }
}
