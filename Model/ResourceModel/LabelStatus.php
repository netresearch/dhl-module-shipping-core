<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\VersionControl\AbstractDb;

/**
 * LabelStatus ResourceModel
 *
 * @package Dhl\ShippingCore\Model\ResourceModel
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class LabelStatus extends AbstractDb
{
    /**
     * Entities with no auto-increment ID must toggle "$_useIsObjectNew" property
     * to distinguish between create and update operations.
     * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::isObjectNotNew
     *
     * @var bool
     */
    protected $_useIsObjectNew = true;

    /**
     * Entities with no auto-increment ID must toggle "$_isPkAutoIncrement" property
     * to preserve the ID field.
     * @see \Magento\Framework\Model\ResourceModel\Db\AbstractDb::saveNewObject
     *
     * @var bool
     */
    protected $_isPkAutoIncrement = false;

    /**
     * Init main table and primary key.
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(Constants::TABLE_DHLGW_LABEL_STATUS, 'order_id');
    }

    /**
     * Determine persistence state.
     *
     * Primary key presence/absence is not an indicator for entities with no auto-increment ID.
     *
     * @param AbstractModel $object
     * @return AbstractDb
     */
    protected function _beforeSave(AbstractModel $object)
    {
        $select = $this->_getLoadSelect($object->getIdFieldName(), $object->getId(), $object);
        $select->limit(1);
        $entityId = $this->getConnection()->fetchOne($select);
        $object->isObjectNew(!$entityId);

        return parent::_beforeSave($object);
    }
}
