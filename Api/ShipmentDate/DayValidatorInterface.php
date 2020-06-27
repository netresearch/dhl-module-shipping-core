<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShipmentDate;

/**
 * @api
 */
interface DayValidatorInterface
{
    /**
     * Returns TRUE if the given date is valid for this validator or FALSE otherwise.
     *
     * @param \DateTime $dateTime The date/time object to check
     * @param mixed $store The store to use for validation
     *
     * @return bool
     */
    public function validate(\DateTime $dateTime, $store = null): bool;
}
