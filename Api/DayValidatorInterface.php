<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use DateTime;
use Magento\Sales\Model\Order;

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
     * @param Order    $order    The current order
     * @param DateTime $dateTime The date/time object to check
     *
     * @return bool
     */
    public function validate(Order $order, DateTime $dateTime): bool;
}
