<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\DayValidatorInterface;
use Dhl\ShippingCore\Api\ConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;

/**
 * The class ShipmentDate calculates the next available shipment date.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class ShipmentDate
{
    /**
     * @var TimezoneInterface
     */
    private $timezone;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var DayValidatorInterface[]
     */
    private $dayValidators;

    /**
     * ShipmentDate constructor.
     *
     * @param TimezoneInterface $timezone
     * @param ConfigInterface $config
     * @param DayValidatorInterface[] $dayValidators A list of validators used to check if the current
     *                                               date can be uses as the shipping date.
     */
    public function __construct(
        TimezoneInterface $timezone,
        ConfigInterface $config,
        array $dayValidators
    ) {
        $this->timezone = $timezone;
        $this->config = $config;
        $this->dayValidators = $dayValidators;
    }

    /**
     * Get the start date.
     *
     * @param mixed $store
     *
     * @return \DateTime
     * @throws \RuntimeException
     */
    public function getDate($store = null): \DateTime
    {
        return $this->getNextPossibleDate(
            $this->timezone->scopeDate($store),
            $this->config->getCutOffTime($store),
            $store
        );
    }

    /**
     * Determines the next possible shipment date.
     *
     * @param \DateTime $shipmentDate The current date/time
     * @param \DateTime $cutOffDateTime The configured cut off date/time
     * @param mixed $store
     *
     * @return \DateTime
     * @throws \RuntimeException
     */
    private function getNextPossibleDate(
        \DateTime $shipmentDate,
        \DateTime $cutOffDateTime,
        $store = null
    ): \DateTime {
        if ($shipmentDate >= $cutOffDateTime) {
            $shipmentDate->modify('+1 day');
        }

        $dayCount = 0;

        do {
            $shipmentDateAllowed = true;

            // Apply all validators to the current date/time
            foreach ($this->dayValidators as $dayValidator) {
                // The validator returns TRUE if the date is valid for it
                $shipmentDateAllowed = $dayValidator->validate($shipmentDate, $store);

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
                throw new \RuntimeException('No valid start date.');
            }
        } while (!$shipmentDateAllowed);

        return $shipmentDate;
    }
}
