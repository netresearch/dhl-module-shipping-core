<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\LabelStatus;

use Dhl\ShippingCore\Model\ResourceModel;
use Magento\Framework\Model\AbstractModel;

class LabelStatus extends AbstractModel
{
    const ORDER_ID = 'order_id';
    const STATUS_CODE = 'status_code';

    /**
     * Initialize LabelStatus resource model.
     */
    protected function _construct()
    {
        $this->_init(ResourceModel\LabelStatus::class);
        parent::_construct();
    }

    /**
     * Get order id.
     *
     * @return int
     */
    public function getOrderId(): int
    {
        return (int) $this->getData(self::ORDER_ID);
    }

    /**
     * Get status code.
     *
     * @return string
     */
    public function getStatusCode(): string
    {
        return (string) $this->getData(self::STATUS_CODE);
    }
}
