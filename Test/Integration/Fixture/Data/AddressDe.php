<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

/**
 * Class AddressDe
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
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
