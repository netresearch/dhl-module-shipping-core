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
     * @var bool
     */
    private $enabledForCheckout = true;

    /**
     * @var bool
     */
    private $enabledForPackaging = true;

    /**
     * @var bool
     */
    private $enabledForAutocreate = true;

    /**
     * @var bool
     */
    private $packagingReadonly = false;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[]
     */
    private $inputs;

    /**
     * @var bool
     */
    private $availableAtPostalFacility = true;

    /**
     * @var string[][]
     */
    private $routes = [];

    /**
     * @var int
     */
    private $sortOrder = 0;

    /**
     * @var int[]
     */
    private $requiredItemIds;

    /**
     * ShippingOption constructor.
     *
     * @param string $code
     * @param string $label
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[] $inputs
     * @param bool $enabledForCheckout
     * @param bool $enabledForPackaging
     * @param bool $enabledForAutocreate
     * @param bool $packagingReadonly
     * @param bool $availableAtPostalFacility
     * @param string[] $routes
     * @param int $sortOrder
     * @param int[] $requiredItemIds
     */
    public function __construct(
        string $code,
        string $label,
        array $inputs,
        bool $enabledForCheckout = true,
        bool $enabledForPackaging = true,
        bool $enabledForAutocreate = true,
        bool $packagingReadonly = false,
        bool $availableAtPostalFacility = true,
        array $routes = [],
        int $sortOrder = 0,
        array $requiredItemIds = []
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->inputs = $inputs;
        $this->enabledForCheckout = $enabledForCheckout;
        $this->enabledForPackaging = $enabledForPackaging;
        $this->enabledForAutocreate = $enabledForAutocreate;
        $this->packagingReadonly = $packagingReadonly;
        $this->availableAtPostalFacility = $availableAtPostalFacility;
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
     * @return bool
     */
    public function isEnabledForCheckout(): bool
    {
        return $this->enabledForCheckout;
    }

    /**
     * @return bool
     */
    public function isEnabledForPackaging(): bool
    {
        return $this->enabledForPackaging;
    }

    /**
     * @return bool
     */
    public function isEnabledForAutocreate(): bool
    {
        return $this->enabledForAutocreate;
    }

    /**
     * @return bool
     */
    public function isPackagingReadonly(): bool
    {
        return $this->packagingReadonly;
    }

    /**
     * @return InputInterface[]
     */
    public function getInputs(): array
    {
        return $this->inputs;
    }

    /**
     * @return bool
     */
    public function isAvailableAtPostalFacility(): bool
    {
        return $this->availableAtPostalFacility;
    }

    /**
     * @return string[][]
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
