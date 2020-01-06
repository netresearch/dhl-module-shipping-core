<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\DeliveryLocation;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface;
use Dhl\ShippingCore\Api\Data\DeliveryLocation\OpeningHoursInterface;

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
    private $dayOfWeek;

    /**
     * @var TimeFrameInterface[]
     */
    private $timeFrames;

    /**
     * @param string $dayOfWeek
     */
    public function setDayOfWeek(string $dayOfWeek)
    {
        $this->dayOfWeek = $dayOfWeek;
    }

    /**
     * @param TimeFrameInterface[] $timeFrames
     */
    public function setTimeFrames(array $timeFrames)
    {
        $this->timeFrames = $timeFrames;
    }

    /**
     * @return string
     */
    public function getDayOfWeek(): string
    {
        return $this->dayOfWeek;
    }

    /**
     * @return TimeFrameInterface[]
     */
    public function getTimeFrames(): array
    {
        return  $this->timeFrames;
    }
}
