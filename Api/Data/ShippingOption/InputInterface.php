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
     * Declare if the input should be presented as read-only.
     *
     * @return bool
     */
    public function isDisabled(): bool;

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
    public function isLabelVisible(): bool;

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

    /**
     * @param string $inputType
     *
     * @return void
     */
    public function setInputType(string $inputType);

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code);

    /**
     * @param string $defaultValue
     *
     * @return void
     */
    public function setDefaultValue(string $defaultValue);

    /**
     * @param bool $disabled
     *
     * @return void
     */
    public function setDisabled(bool $disabled);

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label);

    /**
     * @param bool $labelVisible
     *
     * @return void
     */
    public function setLabelVisible(bool $labelVisible);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface[] $options
     *
     * @return void
     */
    public function setOptions(array $options);

    /**
     * @param string $tooltip
     *
     * @return void
     */
    public function setTooltip(string $tooltip);

    /**
     * @param string $placeholder
     *
     * @return void
     */
    public function setPlaceholder(string $placeholder);

    /**
     * @param int $sortOrder
     *
     * @return void
     */
    public function setSortOrder(int $sortOrder);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface[] $validationRules
     *
     * @return void
     */
    public function setValidationRules(array $validationRules);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface|null $comment
     *
     * @return void
     */
    public function setComment($comment);
}
