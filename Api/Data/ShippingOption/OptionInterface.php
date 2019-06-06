<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface OptionInterface
 *
 * A DTO for an input option, e.g. for a select type input or as part of a radioset.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface OptionInterface
{
    /**
     * May return the unique ID of the option rule.
     * If the option does not have a unique id, it will return ''.
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Retrieve the human-readable name of the option
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Retrieve the value that will be used as value of the parent input while this option is selected.
     *
     * @return string
     */
    public function getValue(): string;

    /**
     * When true, the option will be visible but not user-selectable.
     *
     * @return bool
     */
    public function isDisabled(): bool;

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id);

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label);

    /**
     * @param string $value
     *
     * @return void
     */
    public function setValue(string $value);

    /**
     * @param bool $disabled
     *
     * @return void
     */
    public function setDisabled(bool $disabled);
}
