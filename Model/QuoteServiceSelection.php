<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\Data\Selection\AssignedServiceSelectionInterface;
use Dhl\ShippingCore\Model\ResourceModel\Quote\Address\ServiceSelection;
use Dhl\ShippingCore\Setup\Setup;
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
     * Get the parent id.
     *
     * @return int
     */
    public function getParentId(): int
    {
        return (int) $this->getData(Setup::SERVICE_SELECTION_PARENT_ID);
    }

    /**
     * Get the service code.
     *
     * @return string
     */
    public function getServiceCode(): string
    {
        return (string) $this->getData(Setup::SERVICE_SELECTION_SERVICE_CODE);
    }

    /**
     * Get the input code.
     *
     * @return string
     */
    public function getInputCode(): string
    {
        return (string) $this->getData(Setup::SERVICE_SELECTION_INPUT_CODE);
    }

    /**
     * Get the service value
     *
     * @return string
     */
    public function getValue(): string
    {
        return (string) $this->getData(Setup::SERVICE_SELECTION_VALUE);
    }

}
