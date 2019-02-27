<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface InputInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface InputInterface
{
    const INPUT_TYPE_CHECKBOX = 'checkbox';
    const INPUT_TYPE_DATE     = 'date';
    const INPUT_TYPE_NUMBER   = 'number';
    const INPUT_TYPE_RADIO    = 'radio';
    const INPUT_TYPE_SELECT   = 'select';
    const INPUT_TYPE_TEXT     = 'text';
    const INPUT_TYPE_TIME     = 'time';

    /**
     * Get the display type of the current service input.
     *
     * @return string
     */
    public function getInputType(): string;

    /**
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain the value of a service input.
     * May be boolean true or a date or a monetary value, whatever the service offers.
     *
     * @return string
     */
    public function getDefaultValue(): string;

    /**
     * Obtain the label corresponding to the input
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return bool
     */
    public function hasLabelVisible(): bool;

    /**
     * Obtain a pre-defined set of allowed values.
     *
     * @return \Dhl\ShippingCore\Api\Data\Service\OptionInterface[]
     */
    public function getOptions(): array;

    /**
     * Obtain help text to be displayed with input
     *
     * @return string
     */
    public function getTooltip(): string;

    /**
     * Obtain a placeholder text to be displayed when no value has been entered yet.
     *
     * @return string
     */
    public function getPlaceholder(): string;

    /**
     * Get Sort Order.
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Get rules for user input validation. For a list of mapped rules see:
     *
     * @file view/frontend/web/js/model/service-validation-map.js
     *
     * @return \Dhl\ShippingCore\Api\Data\Service\ValidationRuleInterface[]
     */
    public function getValidationRules(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Service\CommentInterface|null
     */
    public function getComment();
}
