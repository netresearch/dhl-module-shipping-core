<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface OptionInterface
 *
 * @package Dhl\ShippingCore\Api
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
