<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Selection;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection;
use Magento\Framework\Model\AbstractModel;

/**
 * Class QuoteSelection
 * @package Dhl\ShippingCore\Model
 */
class QuoteSelection extends AbstractModel implements AssignedSelectionInterface
{
    /**
     * Initialize Quote Selection resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ShippingOptionSelection::class);
    }

    /**
     * @inheritDoc
     */
    public function getParentId(): int
    {
        return (int) $this->getData(self::PARENT_ID);
    }

    /**
     * @inheritDoc
     */
    public function getShippingOptionCode(): string
    {
        return (string) $this->getData(self::SHIPPING_OPTION_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getInputCode(): string
    {
        return (string) $this->getData(self::INPUT_CODE);
    }

    /**
     * @inheritDoc
     */
    public function getInputValue(): string
    {
        return (string) $this->getData(self::INPUT_VALUE);
    }
}
