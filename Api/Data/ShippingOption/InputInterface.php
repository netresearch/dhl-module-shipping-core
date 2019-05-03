<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface InputInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface InputInterface
{
    const INPUT_TYPE_CHECKBOX = 'checkbox';
    const INPUT_TYPE_DATE = 'date';
    const INPUT_TYPE_NUMBER = 'number';
    const INPUT_TYPE_RADIO = 'radio';
    const INPUT_TYPE_SELECT = 'select';
    const INPUT_TYPE_TEXT = 'text';
    const INPUT_TYPE_TIME = 'time';

    /**
     * Get the display input type of the current shipping option input.
     *
     * @return string
     */
    public function getInputType(): string;

    /**
     * Get the unique identifier code for this input.
     *
     * The input code is only guaranteed to be unique among its parent shipping option's inputs.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain the preconfigured value of a shipping option input.
     *
     * This must always be a string since in HTML inputs, only strings can be transferred as values.
     *
     * @return string
     */
    public function getDefaultValue(): string;

    /**
     * Obtain the human-readable label corresponding to the input.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Declare if the label should be visibly rendered.
     *
     * @return bool
     */
    public function hasLabelVisible(): bool;

    /**
     * Obtain a pre-defined set of allowed values, e.g for a select type input.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface[]
     */
    public function getOptions(): array;

    /**
     * Obtain a help text to be displayed in a tooltip with the input.
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
     * Get sort order of the input among its siblings.
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Get a list of rules for user input validation during runtime. For a list of mapped rules see:
     *
     * @file view/frontend/web/js/model/service-validation-map.js
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface[]
     */
    public function getValidationRules(): array;

    /**
     * Retrieve an optional comment to be displayed with the input.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface|null
     */
    public function getComment();
}