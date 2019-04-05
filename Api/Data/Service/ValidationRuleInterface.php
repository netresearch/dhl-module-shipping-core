<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface ValidationRuleInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface ValidationRuleInterface
{
    /**
     * Name of the validation rule.
     *
     * @see /view/js/mixin/validation.js for custom validation rules.
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
