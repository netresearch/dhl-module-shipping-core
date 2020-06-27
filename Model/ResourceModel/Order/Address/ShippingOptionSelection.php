<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Order\Address;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShippingOptionSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Constants::TABLE_ORDER_SHIPPING_OPTION_SELECTION, 'entity_id');
    }
}
