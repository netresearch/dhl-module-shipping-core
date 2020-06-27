<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingBox;

use Countable;
use IteratorAggregate;

class PackageCollection implements IteratorAggregate, Countable
{
    /**
     * @var Package[]
     */
    private $items;

    /**
     * @var PackageFactory
     */
    private $packageFactory;

    /**
     * PackageCollection constructor.
     *
     * @param PackageFactory $packageFactory
     * @param Package[] $items
     */
    public function __construct(PackageFactory $packageFactory, array $items = [])
    {
        $this->packageFactory = $packageFactory;
        $this->items = $items;
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
