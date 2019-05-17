<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface ShippingOptionInterface
 *
 * A DTO with the rendering information for an individual shipping option with potentially multiple inputs.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ShippingOptionInterface
{
    /**
     * Obtain shipping option code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain shipping option name.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Check if shipping option is enabled for display in checkout.
     *
     * @return bool
     */
    public function isEnabledForCheckout(): bool;

    /**
     * Check if shipping option can be booked by the merchant during shipment creation.
     *
     * @return bool
     */
    public function isEnabledForPackaging(): bool;

    /**
     * Check if shipping option can be booked during autocreate (cron or mass action).
     *
     * @return bool
     */
    public function isEnabledForAutocreate(): bool;

    /**
     * Check if shipping option can be modified by merchant.
     *
     * @return bool
     */
    public function isPackagingReadonly(): bool;

    /**
     * Obtain a list of inputs for displaying the shipping option and its values.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[]
     */
    public function getInputs(): array;

    /**
     * Check if the shipping option can be booked with postal facility deliveries.
     *
     * @return bool
     */
    public function isAvailableAtPostalFacility(): bool;

    /**
     * Obtain routes the shipping option can be booked with.
     *
     * @return mixed
     */
    public function getRoutes(): array;

    /**
     * Retrieve the sort order of the shipping option relative to other shipping options of the current carrier.
     *
     * @return int
     */
    public function getSortOrder(): int;

    /**
     * Returns an array of item ids (order/quote/shipment) that result in the current shipping option being available.
     *
     * @return int[]
     */
    public function getRequiredItemIds(): array;
}
