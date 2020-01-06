<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\DeliveryLocation;

use Dhl\ShippingCore\Api\Data\DeliveryLocation\TimeFrameInterface;

/**
 * Class TimeFrame
 *
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de
 */
class TimeFrame implements TimeFrameInterface
{
    /**
     * @var string
     */
    private $opens;

    /**
     * @var string
     */
    private $closes;

    /**
     * @return string
     */
    public function getOpens(): string
    {
        return $this->opens;
    }

    /**
     * @return string
     */
    public function getCloses(): string
    {
        return $this->closes;
    }

    /**
     * @param string $opens
     */
    public function setOpens(string $opens)
    {
        $this->opens = $opens;
    }

    /**
     * @param string $closes
     */
    public function setCloses(string $closes)
    {
        $this->closes = $closes;
    }
}
