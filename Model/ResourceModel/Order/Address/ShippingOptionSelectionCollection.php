<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Order\Address;

use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\OrderSelection;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection as ServiceSelectionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class ServiceSelectionCollection
 * @package Dhl\ShippingCore\Model\ResourceModel
 */
class ShippingOptionSelectionCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(OrderSelection::class, ServiceSelectionResource::class);
    }
}
