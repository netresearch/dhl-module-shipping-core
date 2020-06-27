<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\Quote\Address;

use Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection\QuoteSelection;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection as ShippingOptionSelectionResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class ShippingOptionSelectionCollection extends AbstractCollection
{
    /**
     * @inheritDoc
     */
    protected function _construct()
    {
        $this->_init(QuoteSelection::class, ShippingOptionSelectionResource::class);
    }
}
