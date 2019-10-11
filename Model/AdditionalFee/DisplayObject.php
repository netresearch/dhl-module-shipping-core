<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\AdditionalFee;

use Magento\Framework\DataObject;

/**
 * Class DisplayObject
 *
 * Bundle data for display purposes
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class DisplayObject extends DataObject
{
    /**
     * @return float
     */
    public function getValueInclTax(): float
    {
        return (float) $this->getData('value_incl_tax');
    }

    /**
     * @return float
     */
    public function getValueExclTax(): float
    {
        return (float) $this->getData('value');
    }

    /**
     * @return float
     */
    public function getBaseValueInclTax(): float
    {
        return (float) $this->getData('base_value_incl_tax');
    }

    /**
     * @return float
     */
    public function getBaseValueExclTax(): float
    {
        return (float) $this->getData('base_value');
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return (string) $this->getData('label');
    }
}
