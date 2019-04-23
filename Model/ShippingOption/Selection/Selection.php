<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Selection;

use Dhl\ShippingCore\Api\Data\ShippingOption\Selection\SelectionInterface;

/**
 * Class Selection
 *
 * @package Dhl\ShippingCore\Api\Data
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Selection implements SelectionInterface
{
    /**
     * @var string
     */
    private $shippingOptionCode;

    /**
     * @var string
     */
    private $inputCode;

    /**
     * @var string
     */
    private $inputValue;

    /**
     * Selection constructor.
     *
     * @param string $shippingOptionCode
     * @param string $inputCode
     * @param string $inputValue
     */
    public function __construct(string $shippingOptionCode, string $inputCode, string $inputValue)
    {
        $this->shippingOptionCode = $shippingOptionCode;
        $this->inputCode = $inputCode;
        $this->inputValue = $inputValue;
    }

    /**
     * @return string
     */
    public function getShippingOptionCode(): string
    {
        return $this->shippingOptionCode;
    }

    /**
     * @return string
     */
    public function getInputCode(): string
    {
        return $this->inputCode;
    }

    /**
     * @return string
     */
    public function getInputValue(): string
    {
        return $this->inputValue;
    }
}
