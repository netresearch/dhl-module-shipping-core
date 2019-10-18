<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Sales;

/**
 * Interface ShippingOptionExtensionInterface
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
interface ShippingOptionInterface
{
    const PACKAGE = 'package';
    const SERVICES = 'services';

    /**
     * @return \Dhl\ShippingCore\Api\Data\KeyValueObjectInterface[]
     */
    public function getPackage(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Sales\ServiceDataInterface[]
     */
    public function getServices(): array;
}
