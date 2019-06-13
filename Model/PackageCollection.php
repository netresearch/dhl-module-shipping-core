<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Countable;
use IteratorAggregate;

/**
 * Class PackageCollection
 *
 * @package Dhl\ShippingCore\Model
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class PackageCollection implements IteratorAggregate, Countable
{
    /**
     * @var Package[]
     */
    private $items;

    /**
     * @var \Dhl\ShippingCore\Model\PackageFactory
     */
    private $packageFactory;

    /**
     * PackageCollection constructor.
     *
     * @param Package[] $items
     * @param \Dhl\ShippingCore\Model\PackageFactory $packageFactory
     */
    public function __construct(\Dhl\ShippingCore\Model\PackageFactory $packageFactory, array $items = [])
    {
        $this->items = $items;
        $this->packageFactory = $packageFactory;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->items);
    }

    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }

    /**
     * @param Package $package
     */
    public function addPackage(Package $package)
    {
        $this->items[] = $package;
    }

    /**
     * @param array $packageData
     */
    public function addPackageAsArray(array $packageData)
    {
        $this->addPackage($this->packageFactory->create(['data' => $packageData]));
    }

    /**
     * @param array $packages
     */
    public function addFromArray(array $packages)
    {
        foreach ($packages as $packageData) {
            $this->addPackageAsArray($packageData);
        }
    }

    /**
     * @return Package|null
     */
    public function getDefaultPackage()
    {
        $packages = array_filter(
            $this->items,
            function (Package $package) {
                return $package->isDefault();
            }
        );

        return array_shift($packages);
    }
}
