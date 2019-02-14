<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface ServiceCompatibilityInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface ServiceCompatibilityInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string[]
     */
    public function getSubject(): array;
}
