<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\OrderExport;

/**
 * Interface ShippingOptionInterface
 *
 * @api
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
interface ShippingOptionInterface
{
    const PACKAGE = 'package';
    const SERVICES = 'services';

    /**
     * @return \Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface[]
     */
    public function getPackage(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\OrderExport\ServiceDataInterface[]
     */
    public function getServices(): array;
}
