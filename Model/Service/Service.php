<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Service\InputInterface;
use Dhl\ShippingCore\Api\Data\Service\ServiceInterface;

/**
 * Class Service
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class Service implements ServiceInterface
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
    private $enabledForCheckout;

    /**
     * @var bool
     */
    private $enabledForPackaging;

    /**
     * @var bool
     */
    private $enabledForAutocreate;

    /**
     * @var bool
     */
    private $packagingReadonly;

    /**
     * @var InputInterface[]
     */
    private $inputs;

    /**
     * @var bool
     */
    private $availableAtPostalFacility;

    /**
     * @var string[][]
     */
    private $routes;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * Service constructor.
     *
     * @param string $code
     * @param string $label
     * @param bool $enabledForCheckout
     * @param bool $enabledForPackaging
     * @param bool $enabledForAutocreate
     * @param bool $packagingReadonly
     * @param InputInterface[] $inputs
     * @param bool $availableAtPostalFacility
     * @param \string[][] $routes
     * @param int $sortOrder
     */
    public function __construct(
        string $code,
        string $label,
        bool $enabledForCheckout,
        bool $enabledForPackaging,
        bool $enabledForAutocreate,
        bool $packagingReadonly,
        array $inputs,
        bool $availableAtPostalFacility,
        array $routes,
        int $sortOrder
    ) {
        $this->code = $code;
        $this->label = $label;
        $this->enabledForCheckout = $enabledForCheckout;
        $this->enabledForPackaging = $enabledForPackaging;
        $this->enabledForAutocreate = $enabledForAutocreate;
        $this->packagingReadonly = $packagingReadonly;
        $this->inputs = $inputs;
        $this->availableAtPostalFacility = $availableAtPostalFacility;
        $this->routes = $routes;
        $this->sortOrder = $sortOrder;
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
     * @return \Dhl\ShippingCore\Api\Data\Service\InputInterface[]
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
}
