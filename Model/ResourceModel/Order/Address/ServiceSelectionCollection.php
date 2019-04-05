<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Order\Address;

use Dhl\ShippingCore\Model\OrderServiceSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ServiceSelection as ServiceSelectionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class ServiceSelectionCollection
 *
 * @package Dhl\ShippingCore\Model\ResourceModel\Order\Address
 */
class ServiceSelectionCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
   protected function _construct()
    {
        $this->_init(OrderServiceSelection::class, ServiceSelectionResource::class);
    }
}
