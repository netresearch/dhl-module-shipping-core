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
interface ItemShippingOptionsInterface
{
    /**
     * @return int
     */
    public function getItemId(): int;

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    public function getShippingOptions(): array;

    /**
     * @param int $itemId
     *
     * @return void
     */
    public function setItemId(int $itemId);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[] $shippingOptions
     *
     * @return void
     */
    public function setShippingOptions(array $shippingOptions);
}
