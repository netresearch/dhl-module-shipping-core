<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Selection;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection;
use Magento\Framework\Model\AbstractModel;

/**
 * Class OrderSelection
 *
 * @package Dhl\ShippingCore\Model
 */
class OrderSelection extends AbstractModel implements AssignedSelectionInterface
{
    /**
     * Initialize Order Selection resource model
     */
    protected function _construct()
    {
        $this->_init(ShippingOptionSelection::class);
        parent::_construct();
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
