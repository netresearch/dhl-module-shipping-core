<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection;
use Magento\Framework\Model\AbstractModel;

/**
 * Class QuoteServiceSelection
 * @package Dhl\ShippingCore\Model
 */
class QuoteServiceSelection extends AbstractModel implements AssignedServiceSelectionInterface
{
    /**
     * Initialize Quote ServiceSelection resource model
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init(ServiceSelection::class);
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
    public function getServiceCode(): string
    {
        return (string) $this->getData(self::SERVICE_CODE);
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
    public function getValue(): string
    {
        return (string) $this->getData(self::VALUE);
    }
}
