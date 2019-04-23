<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface ValidationRuleInterface
 *
 * A DTO to represent a validation rule to be used at runtime to validate user input.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
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
     * Parameters available to the validation rule (eg max number of characters)
     *
     * @return mixed
     */
    public function getParams();
}
