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
     * Obtain shipping option available config path.
     *
     * @return string
     */
    public function getAvailable(): string;

    /**
     * Obtain shipping option name.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Obtain a list of inputs for displaying the shipping option and its values.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[]
     */
    public function getInputs(): array;

    /**
     * Obtain routes the shipping option can be booked with.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface[]
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

    /**
     * @param string $code
     *
     * @return void
     */
    public function setCode(string $code);

    /**
     * @param string $available
     *
     * @return void
     */
    public function setAvailable(string $available);

    /**
     * @param string $label
     *
     * @return void
     */
    public function setLabel(string $label);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface[] $inputs
     *
     * @return void
     */
    public function setInputs(array $inputs);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\RouteInterface[] $routes
     *
     * @return void
     */
    public function setRoutes(array $routes);

    /**
     * @param int $sortOrder
     *
     * @return void
     */
    public function setSortOrder(int $sortOrder);

    /**
     * @param int[] $requiredItemIds
     *
     * @return void
     */
    public function setRequiredItemIds(array $requiredItemIds);
}
