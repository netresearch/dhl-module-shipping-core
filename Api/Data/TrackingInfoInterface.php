<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

/**
 * Class TrackingInfoInterface.
 *
 * @package Dhl\ShippingCore\Api
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
interface TrackingInfoInterface
{
    /**
     * Field names used to store the data.
     */
    public const DATA_TRACKING_NUMBER              = 'tracking_number';
    public const DATA_PICKUP_COUNTRY               = 'pickup_country';
    public const DATA_PICKUP_DATE                  = 'pickup_date';
    public const DATA_DISPATCH_CONFIRMATION_NUMBER = 'dispatch_confirmation_number';

    /**
     * Pickup date format.
     */
    public const PICKUP_DATE_FORMAT = 'Y-m-d';

    /**
     * Returns the tracking number.
     *
     * @return null|string
     */
    public function getTrackingNumber(): ?string;

    /**
     * Sets the tracking number.
     *
     * @param string $trackingNumber The tracking number
     *
     * @return TrackingInfoInterface
     */
    public function setTrackingNumber(string $trackingNumber): TrackingInfoInterface;

    /**
     * Returns the pickup country code.
     *
     * @return null|string
     */
    public function getPickupCountry(): ?string;

    /**
     * Sets the pickup country code.
     *
     * @param string $pickupCountry The pickup country code
     *
     * @return TrackingInfoInterface
     */
    public function setPickupCountry(string $pickupCountry): TrackingInfoInterface;

    /**
     * Returns the pickup date.
     *
     * @return null|string
     */
    public function getPickupDate(): ?string;

    /**
     * Sets the pickup date.
     *
     * @param int|string|\DateTime $pickupDate The pickup date
     *
     * @return TrackingInfoInterface
     */
    public function setPickupDate($pickupDate): TrackingInfoInterface;

    /**
     * Returns the dispatch confirmation number.
     *
     * @return null|string
     */
    public function getDispatchConfirmationNumber(): ?string;

    /**
     * Sets the dispatch confirmation number.
     *
     * @param string $dispatchConfirmationNumber The dispatch confirmation number
     *
     * @return TrackingInfoInterface
     */
    public function setDispatchConfirmationNumber(string $dispatchConfirmationNumber): TrackingInfoInterface;
}
