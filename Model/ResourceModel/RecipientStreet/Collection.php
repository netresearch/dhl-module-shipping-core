<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\RecipientStreet;

use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Dhl\ShippingCore\Model\SplitAddress\RecipientStreet;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Initialization
     */
    public function _construct()
    {
        $this->_init(RecipientStreet::class, RecipientStreetResource::class);
    }
}
