<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

class AddressDe implements AddressInterface
{
    public function getStreet(): string
    {
        return 'Charles-de-Gaulle-Straße 20';
    }

    public function getCity(): string
    {
        return 'Bonn';
    }

    public function getPostcode(): string
    {
        return '53113';
    }

    public function getCountryId(): string
    {
        return 'DE';
    }

    public function getRegionId(): string
    {
        return 'NRW';
    }
}
