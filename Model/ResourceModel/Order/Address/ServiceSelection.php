<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Order\Address;

use Dhl\ShippingCore\Setup\Setup;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class ServiceSelection
 *
 * @package Dhl\ShippingCore\Model\ResourceModel
 */
class ServiceSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Setup::TABLE_ORDER_SERVICE_SELECTION, 'entity_id');
    }
}
