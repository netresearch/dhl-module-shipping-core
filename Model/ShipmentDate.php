<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use DateTime;
use Dhl\ShippingCore\Api\DayValidatorInterface;
use Dhl\ShippingCore\Model\Config\Config;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterfaceFactory;
use Magento\Sales\Model\Order;

/**
 * The class ShipmentDate calculates the next available shipment date.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class ShipmentDate
{
    const WEEKDAY_SUNDAY = '7';

    /**
     * @var TimezoneInterfaceFactory
     */
    private $timezoneFactory;

    /**
     * @var Config
     */
    private $config;

    /**
     * @var DayValidatorInterface[]
     */
    private $dayValidators;

    /**
     * ShipmentDate constructor.
     *
     * @param TimezoneInterfaceFactory $timezoneFactory
     * @param Config $config
     * @param DayValidatorInterface[] $dayValidators A list of validators used to check if the current
     *                                               date can be uses as the shipping date.
     */
    public function __construct(
        TimezoneInterfaceFactory $timezoneFactory,
        Config $config,
        array $dayValidators
    ) {
        $this->timezoneFactory = $timezoneFactory;
        $this->config = $config;
        $this->dayValidators = $dayValidators;
    }

    /**
     * Get the start date.
     *
     * @param Order $order
     *
     * @return DateTime
     * @throws LocalizedException
     */
    public function getDate(Order $order): DateTime
    {
        return $this->getNextPossibleDate(
            $this->timezoneFactory->create()->date(),
            $this->config->getCutOffTime($order->getStoreId()),
            $order
        );
    }

    /**
     * Determines the next possible shipment date.
     *
     * @param DateTime $shipmentDate The current date/time
     * @param DateTime $cutOffDateTime  The configured cut off date/time
     * @param Order    $order
     *
     * @return DateTime
     * @throws LocalizedException
     */
    private function getNextPossibleDate(
        DateTime $shipmentDate,
        DateTime $cutOffDateTime,
        Order $order
    ): DateTime {
        if ($shipmentDate >= $cutOffDateTime) {
            $shipmentDate->modify('+1 day');
        }

        $dayCount = 0;

        do {
            $shipmentDateAllowed = true;

            // Apply all validators to the current date/time
            foreach ($this->dayValidators as $dayValidator) {
                // The validator returns TRUE if the date is valid for it
                $shipmentDateAllowed = $dayValidator->validate($order, $shipmentDate);

                // All validators have to agree that a date is valid before it can be used
                if (!$shipmentDateAllowed) {
                    break;
                }
            }

            // If current date is a date where no package can be handed over, try the next day
            if (!$shipmentDateAllowed) {
                $shipmentDate->modify('+1 day');
            }

            $dayCount++;

            // If merchant has a bad configuration eg. all days marked as non drop off days we need to exit the loop.
            // Exception is thrown and service will be removed from the array.
            if ($dayCount === 6) {
                throw new LocalizedException(__('No valid start date.'));
            }
        } while (!$shipmentDateAllowed);

        return $shipmentDate;
    }
}