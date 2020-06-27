<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

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
    public function getPostcode(): string;

    /**
     * @return string
     */
    public function getCountryId(): string;

    /**
     * @return string
     */
    public function getRegionId(): string;
}
