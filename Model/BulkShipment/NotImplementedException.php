<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\BulkShipment;

use Magento\Framework\Exception\LocalizedException;

/**
 * Class NotImplementedException
 *
 * Exception can be used to indicate that
 * - the requested bulk configuration is not available
 * - the requested bulk operation is not supported
 *
 * @api
 * @package Dhl\ShippingCore\Model
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class NotImplementedException extends LocalizedException
{
}
