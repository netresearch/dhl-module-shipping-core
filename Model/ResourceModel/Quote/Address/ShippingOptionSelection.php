<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Quote\Address;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * Class Selection
 * @package Model\ResourceModel\Quote\Address
 */
class ShippingOptionSelection extends AbstractDb
{
    /**
     * Resource initialization
     */
    protected function _construct()
    {
        $this->_init(Constants::TABLE_QUOTE_SHIPPING_OPTION_SELECTION, 'entity_id');
    }
}
