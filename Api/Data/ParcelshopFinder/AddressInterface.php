<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ParcelshopFinder;

/**
 * Interface AddressInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
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
    public function getCountry(): string;

    /**
     * @return string
     */
    public function getPostalCode(): string;

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
    public function setCountry(string $string);

    /**
     * @param string $string
     * @return void
     */
    public function setPostalCode(string $string);
}
