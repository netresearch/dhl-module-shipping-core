<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\DeliveryLocation;

/**
 * @api
 */
interface AddressInterface
{
    /**
     * @return string
     */
    public function getStreet(): string;

    /**
     * @return string
     */
    public function getCity(): string;

    /**
     * @return string
     */
    public function getCountryCode(): string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

    /**
     * @return string
     */
    public function getCompany(): string;

    /**
     * @param string $string
     * @return void
     */
    public function setStreet(string $string);

    /**
     * @param string $string
     * @return void
     */
    public function setCity(string $string);

    /**
     * @param string $string
     * @return void
     */
    public function setCountryCode(string $string);

    /**
     * @param string $string
     * @return void
     */
    public function setPostalCode(string $string);

    /**
     * @param string $string
     * @return void
     */
    public function setCompany(string $string);
}
