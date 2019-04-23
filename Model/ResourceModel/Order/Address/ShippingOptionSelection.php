<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Order\Address;

use Dhl\ShippingCore\Setup\SetupSchema;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class Selection
 * @package Dhl\ShippingCore\Model\ResourceModel
 */
class ShippingOptionSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(SetupSchema::TABLE_ORDER_SHIPPING_OPTION_SELECTION, 'entity_id');
    }
}
