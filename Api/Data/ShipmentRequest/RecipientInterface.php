<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentRequest;

/**
 * Interface RecipientInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface RecipientInterface
{
    /**
     * Obtain recipient full name.
     *
     * @return string
     */
    public function getContactPersonName(): string;

    /**
     * Obtain recipient first name.
     *
     * @return string
     */
    public function getContactPersonFirstName(): string;

    /**
     * Obtain recipient last name.
     *
     * @return string
     */
    public function getContactPersonLastName(): string;

    /**
     * Obtain recipient email.
     *
     * @return string
     */
    public function getContactEmail(): string;

    /**
     * Obtain recipient company name.
     *
     * @return string
     */
    public function getContactCompanyName(): string;

    /**
     * Obtain recipient phone number.
     *
     * @return string
     */
    public function getContactPhoneNumber(): string;

    /**
     * Obtain recipient street (1-3 street parts).
     *
     * @return string[]
     */
    public function getStreet(): array;

    /**
     * Obtain shipper street name.
     *
     * @return string
     */
    public function getStreetName(): string;

    /**
     * Obtain shipper street number.
     *
     * @return string
     */
    public function getStreetNumber(): string;

    /**
     * Obtain shipper address addition.
     *
     * @return string
     */
    public function getAddressAddition(): string;

    /**
     * Obtain recipient city.
     *
     * @return string
     */
    public function getCity(): string;

    /**
     * Obtain recipient company state or province.
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Obtain recipient postal code.
     *
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * Obtain recipient country code.
     *
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * Obtain recipient region code.
     *
     * @return string
     */
    public function getRegionCode(): string;
}
