<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface ServiceSelectionInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface ServiceSelectionInterface
{
    /**
     * @return string
     */
    public function getServiceCode(): string;

    /**
     * @return string
     */
    public function getInputCode(): string;

    /**
     * @return string
     */
    public function getValue(): string;
}
