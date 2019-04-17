<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface OptionInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface OptionInterface
{
    /**
     * @return string
     */
    public function getLabel(): string;

    /**
     * @return string
     */
    public function getValue(): string;

    /**
     * @return bool
     */
    public function isDisabled(): bool;
}
