<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface AssignedServiceSelectionInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface AssignedServiceSelectionInterface extends ServiceSelectionInterface
{
    /**
     * @return int
     */
    public function getParentId(): int;
}
