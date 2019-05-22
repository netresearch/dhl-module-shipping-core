<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;

/**
 * Class ShippingOption
 *
 * @package Dhl\ShippingCore\Model
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
    private $label;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[]
     */
    private $inputs;

    /**
     * @var mixed
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
     * ShippingOption constructor.
     *
     * @param string $code
     * @param string $label
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[] $inputs
     * @param string[] $routes
     * @param int $sortOrder
     * @param int[] $requiredItemIds
     */
    public function __construct(
        string $code,
        string $label,
        array $inputs,
        array $routes = [],
        int $sortOrder = 0,
        array $requiredItemIds = []
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->inputs = $inputs;
        $this->routes = $routes;
        $this->sortOrder = $sortOrder;
        $this->requiredItemIds = $requiredItemIds;
    }

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
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return InputInterface[]
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return mixed
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

}
