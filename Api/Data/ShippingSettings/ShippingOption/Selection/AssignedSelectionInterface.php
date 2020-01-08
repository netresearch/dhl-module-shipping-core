<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection;

/**
 * Interface AssignedSelectionInterface
 *
 * A shipping option selection that has been assigned to a specific Quote or Order address id.
 *
 * @api
 */
interface AssignedSelectionInterface extends SelectionInterface
{
    const PARENT_ID = 'parent_id';

    /**
     * Get the parent id which can be either a Quote address Id or an Order address Id.
     *
     * @return int
     */
    public function getParentId(): int;
}
