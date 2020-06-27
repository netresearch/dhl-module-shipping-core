<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\DeliveryLocation;

/**
 * @api
 */
interface TimeFrameInterface
{
    /**
     * @return string
     */
    public function getCloses(): string;

    /**
     * @return string
     */
    public function getOpens(): string;
}
