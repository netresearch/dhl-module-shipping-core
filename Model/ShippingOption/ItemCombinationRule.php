<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemCombinationRuleInterface;

/**
 * Class ItemCombinationRule
 *
 * @package Dhl\ShippingCore\Model
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ItemCombinationRule implements ItemCombinationRuleInterface
{
    /**
     * @var string
     */
    private $sourceItemInputCode;

    /**
     * @var string
     */
    private $action;

    /**
     * @return string
     */
    public function getSourceItemInputCode(): string
    {
        return $this->sourceItemInputCode;
    }

    /**
     * @param string $sourceItemInputCode
     */
    public function setSourceItemInputCode(string $sourceItemInputCode)
    {
        $this->sourceItemInputCode = $sourceItemInputCode;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }
}
