<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\ShippingOption\Selection;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\AssignedSelectionInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ShippingOptionSelection;
use Magento\Framework\Model\AbstractModel;

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
     * @param string $shippingOptionCode
     *
     * @return SelectionInterface
     */
    public function setShippingOptionCode(string $shippingOptionCode): SelectionInterface
    {
        $this->setData(self::SHIPPING_OPTION_CODE, $shippingOptionCode);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputCode(): string
    {
        return (string) $this->getData(self::INPUT_CODE);
    }

    /**
     * @param string $inputCode
     *
     * @return SelectionInterface
     */
    public function setInputCode(string $inputCode): SelectionInterface
    {
        $this->setData(self::INPUT_CODE, $inputCode);
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getInputValue(): string
    {
        return (string) $this->getData(self::INPUT_VALUE);
    }

    /**
     * @param string $inputValue
     *
     * @return SelectionInterface
     */
    public function setInputValue(string $inputValue): SelectionInterface
    {
        $this->setData(self::INPUT_VALUE, $inputValue);
        return $this;
    }
}
