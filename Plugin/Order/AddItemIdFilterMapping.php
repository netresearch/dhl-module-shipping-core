<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Magento\Sales\Model\ResourceModel\Order\Item\Collection;

/**
 * Add field mapping for the order item collection's primary key.
 *
 * When the order item collection is loaded, then the DHL module adds some
 * extension attributes. The database table that holds the additional attributes
 * also has a field `item_id` (both primary and foreign key constraint). This
 * leads to an integrity constraint/ambiguous column error when the `item_id`
 * filter is set with no table alias. To fix this, we add the filter mapping.
 *
 * @see \Magento\Sales\Model\ResourceModel\Order\Item\Collection::addIdFilter
 * @see \Dhl\ShippingCore\Observer\JoinOrderItemAttributes
 */
class AddItemIdFilterMapping
{
    /**
     * @param Collection $collection
     */
    public function beforeAddIdFilter(Collection $collection)
    {
        $collection->addFilterToMap('item_id', 'main_table.item_id');
    }
}
