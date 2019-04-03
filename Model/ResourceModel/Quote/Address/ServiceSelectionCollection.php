<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Quote\Address;

use Dhl\ShippingCore\Model\Service\ServiceSelection as ServiceSelectionModel;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection as ServiceSelectionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Class ServiceSelectionCollection
 *
 * @package Model\ResourceModel\Quote\Address
 */
class ServiceSelectionCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(ServiceSelectionModel::class, ServiceSelectionResource::class);
    }
}
