<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface AssignedServiceSelectionInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface AssignedServiceSelectionInterface extends ServiceSelectionInterface
{
    const PARENT_ID = 'parent_id';

    /**
     * Get the parent id, i.e. quote/order address id
     *
     * @return int
     */
    public function getParentId(): int;
}
