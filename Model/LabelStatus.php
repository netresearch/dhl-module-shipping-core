<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\ResourceModel\LabelStatus as LabelStatusResource;
use Magento\Framework\Model\AbstractModel;

/**
 * LabelStatus
 *
 * @package Dhl\ShippingCore\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class LabelStatus extends AbstractModel
{
    const ORDER_ID = 'order_id';
    const STATUS_CODE = 'status_code';

    /**
     * Initialize LabelStatus resource model.
     */
    protected function _construct()
    {
        $this->_init(LabelStatusResource::class);
        parent::_construct();
    }

    /**
     * Get order id.
     *
     * @return int
     */
    public function getOrderId(): int
    {
        return $this->getData(self::ORDER_ID);
    }

    /**
     * Get status code.
     *
     * @return string
     */
    public function getStatusCode(): string
    {
        return $this->getData(self::STATUS_CODE);
    }
}
