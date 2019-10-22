<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ParcelshopFinder;

/**
 * Interface OpeningHoursInterface
 *
 * @package Dhl\ShippingCore\Api\Data
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface OpeningHoursInterface
{
    /**
     * @return string
     */
    public function getCloses(): string;

    /**
     * @return string
     */
    public function getDayOfWeek(): string;

    /**
     * @return string
     */
    public function getOpens(): string;
}
