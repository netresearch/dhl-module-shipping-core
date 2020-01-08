<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface ValidationRuleInterface
 *
 * A DTO to represent a validation rule to be used at runtime to validate user input.
 *
 * @api
 */
interface ValidationRuleInterface
{
    /**
     * Name of the validation rule.
     *
     * @file /view/js/mixin/validation.js for custom validation rules.
     * @return string
     */
    public function getName(): string;

    /**
     * Parameter available to the validation rule (eg max number of characters)
     *
     * @return mixed|null
     */
    public function getParam();

    /**
     * @param string $name
     *
     * @return void
     */
    public function setName(string $name);

    /**
     * @param mixed $param
     *
     * @return void
     */
    public function setParam($param);
}
