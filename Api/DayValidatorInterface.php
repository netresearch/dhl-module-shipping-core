<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

/**
 * Day validator interface.
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface DayValidatorInterface
{
    /**
     * Returns TRUE if the given date is valid for this validator or FALSE otherwise.
     *
     * @param \DateTime $dateTime The date/time object to check
     * @param int|null $storeId  The current store id
     *
     * @return bool
     */
    public function validate(\DateTime $dateTime, int $storeId = null): bool;
}
