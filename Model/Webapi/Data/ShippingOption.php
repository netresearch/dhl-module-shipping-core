<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Webapi\Data;

use Dhl\ShippingCore\Api\Data\KeyValueObjectInterface;
use Dhl\ShippingCore\Api\Data\Sales\ServiceDataInterface;
use Dhl\ShippingCore\Api\Data\Sales\ShippingOptionInterface;

/**
 * Class ShippingOption
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class ShippingOption implements ShippingOptionInterface
{
    /**
     * @var KeyValueObjectInterface[]
     */
    private $package;

    /**
     * @var ServiceDataInterface[]
     */
    private $services;

    /**
     * PackageData constructor.
     *
     * @param KeyValueObjectInterface[] $package
     * @param ServiceDataInterface[] $services
     */
    public function __construct(array $package, array $services)
    {
        $this->package = $package;
        $this->services = $services;
    }

    /**
     * @return KeyValueObjectInterface[]
     */
    public function getPackage(): array
    {
        return $this->package;
    }

    /**
     * @param KeyValueObjectInterface[] $package
     * @return $this
     */
    public function setPackage(array $package)
    {
        $this->package = $package;

        return $this;
    }

    /**
     * @return ServiceDataInterface[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    /**
     * @param ServiceDataInterface[] $services
     * @return $this
     */
    public function setServices(array $services)
    {
        $this->services = $services;

        return $this;
    }
}
