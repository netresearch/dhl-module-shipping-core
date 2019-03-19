<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel;

use Dhl\ShippingCore\Setup\Setup;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * LabelStatus ResourceModel
 *
 * @package Dhl\ShippingCore\Model\ResourceModel
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class LabelStatus extends AbstractDb
{
    /**
     * Init main table and primary key.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Setup::TABLE_LABEL_STATUS, 'entity_id');
    }
}
