<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Sales\Api\Data\ShipmentItemInterface;

/**
 * Interface ShipmentItemFilterInterface
 *
 * Extract shippable items from a list of shipment items.
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de
 */
interface ShipmentItemFilterInterface
{
    /**
     * From the given shipment items, return only those that actually get shipped.
     *
     * Items to ship:
     * - configurables
     * - bundles if shipped together
     * - the bundle's simples if shipped separately
     * - standalone simples
     *
     * @param ShipmentItemInterface[] $items
     * @return ShipmentItemInterface[]
     */
    public function getShippableItems(array $items): array;
}
