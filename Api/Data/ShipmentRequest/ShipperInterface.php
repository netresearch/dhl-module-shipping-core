<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentRequest;

/**
 * Interface ShipperInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ShipperInterface
{
    /**
     * Obtain shipper full name.
     *
     * @return string
     */
    public function getContactPersonName(): string;

    /**
     * Obtain shipper first name.
     *
     * @return string
     */
    public function getContactPersonFirstName(): string;

    /**
     * Obtain shipper last name.
     *
     * @return string
     */
    public function getContactPersonLastName(): string;

    /**
     * Obtain shipper company name.
     *
     * @return string
     */
    public function getContactCompanyName(): string;

    /**
     * Obtain shipper email.
     *
     * @return string
     */
    public function getContactEmail(): string;

    /**
     * Obtain shipper phone number.
     *
     * @return string
     */
    public function getContactPhoneNumber(): string;

    /**
     * Obtain shipper street (1-3 street parts).
     *
     * @return string[]
     */
    public function getStreet(): array;

    /**
     * Obtain shipper city.
     *
     * @return string
     */
    public function getCity(): string;

    /**
     * Obtain shipper company state or province.
     *
     * @return string
     */
    public function getState(): string;

    /**
     * Obtain shipper postal code.
     *
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * Obtain shipper country code.
     *
     * @return string
     */
    public function getCountryCode(): string;
}