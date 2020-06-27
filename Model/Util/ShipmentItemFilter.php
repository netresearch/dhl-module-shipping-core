<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * Extract shippable items from a list of shipment items.
 */
class ShipmentItemFilter
{
    /**
     * From the given shipment items, return only those that actually get shipped.
     *
     * @param ShipmentItemInterface[] $items
     * @return ShipmentItemInterface[]
     */
    public function getShippableItems(array $items): array
    {
        $fnFilter = function (Item $item) {
            $orderItem = $item->getOrderItem();
            $hasParent = $orderItem->getParentItemId() || $orderItem->getParentItem();

            if (!$hasParent && $orderItem->isShipSeparately()) {
                // the separate items' container – will not be shipped
                return false;
            }

            if ($hasParent && !$orderItem->isShipSeparately()) {
                // the bundle's simple items – all shipped together with the parent
                return false;
            }

            return true;
        };

        return \array_filter($items, $fnFilter);
    }
}
