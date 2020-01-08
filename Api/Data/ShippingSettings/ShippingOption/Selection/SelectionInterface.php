<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection;

/**
 * Interface SelectionInterface
 *
 * A DTO that represents a customer's selection of an individual shipping option value.
 *
 * @api
 */
interface SelectionInterface
{
    const SHIPPING_OPTION_CODE = 'shipping_option_code';

    const INPUT_CODE = 'input_code';

    const INPUT_VALUE = 'input_value';

    /**
     * Get the shipping option code, e.g. "packstation".
     *
     * @return string
     */
    public function getShippingOptionCode(): string;

    /**
     * @param string $shippingOptionCode
     *
     * @return SelectionInterface
     */
    public function setShippingOptionCode(string $shippingOptionCode): SelectionInterface;

    /**
     * Get the input code, e.g. "packstationNumber" or "postNumber".
     *
     * @return string
     */
    public function getInputCode(): string;

    /**
     * @param string $inputCode
     *
     * @return SelectionInterface
     */
    public function setInputCode(string $inputCode): SelectionInterface;

    /**
     * Get the input value, e.g. 520 (packstationNumber) or 12345678 (postNumber).
     *
     * @return string
     */
    public function getInputValue(): string;

    /**
     * @param string $inputValue
     *
     * @return SelectionInterface
     */
    public function setInputValue(string $inputValue): SelectionInterface;
}
