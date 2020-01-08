<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

/**
 * RecipientStreetInterface
 *
 * @api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface RecipientStreetInterface
{
    const ORDER_ADDRESS_ID = 'order_address_id';
    const NAME = 'name';
    const NUMBER = 'number';
    const SUPPLEMENT = 'supplement';

    /**
     * Get the order address id.
     *
     * @return int|null
     */
    public function getOrderAddressId();

    /**
     * Get street name.
     *
     * @return string
     */
    public function getName(): string;

    /**
     * Get street number.
     *
     * @return string
     */
    public function getNumber(): string;

    /**
     * Get supplement.
     *
     * @return string
     */
    public function getSupplement(): string;
}
