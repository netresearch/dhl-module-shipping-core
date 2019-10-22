<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ParcelshopFinder;

use Dhl\ShippingCore\Api\Data\ParcelshopFinder\OpeningHoursInterface;

/**
 * Class OpeningHours
 *
 * @package Dhl\ShippingCore\Model
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
class OpeningHours implements OpeningHoursInterface
{
    /**
     * @var string
     */
    private $closes;

    /**
     * @var string
     */
    private $dayOfWeek;

    /**
     * @var string
     */
    private $opens;

    /**
     * @param string $closes
     */
    public function setCloses(string $closes)
    {
        $this->closes = $closes;
    }

    /**
     * @param string $dayOfWeek
     */
    public function setDayOfWeek(string $dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @param string $opens
     */
    public function setOpens(string $opens)
    {
        $this->opens = $opens;
    }

    /**
     * @return string
     */
    public function getCloses(): string
    {
        return $this->closes;
    }

    /**
     * @return string
     */
    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    /**
     * @return string
     */
    public function getOpens(): string
    {
        return $this->opens;
    }
}
