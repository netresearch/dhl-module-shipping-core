<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\DeliveryLocation;

/**
 * Interface TimeFrameInterface
 *
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
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
