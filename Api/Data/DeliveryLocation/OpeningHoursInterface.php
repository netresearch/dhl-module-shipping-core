<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\DeliveryLocation;

/**
 * @api
 */
interface OpeningHoursInterface
{
    /**
     * @return \Dhl\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface[]
     */
    public function getTimeFrames(): array;

    /**
     * @return string
     */
    public function getDayOfWeek(): string;
}
