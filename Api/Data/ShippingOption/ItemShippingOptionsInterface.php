<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingOption;

/**
 * Interface ItemShippingOptionsInterface
 *
 * A DTO that acts as a container for shipping options that apply to a specific shipment item.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ItemShippingOptionsInterface
{
    /**
     * The shipment order item id the shipping options apply to.
     *
     * @return int
     */
    public function getItemId(): int;

    /**
     * The shipping options that are specific to the item id.
     *
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
