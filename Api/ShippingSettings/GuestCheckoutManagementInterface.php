<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings;

use Dhl\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;

/**
 * Interface GuestCheckoutManagementInterface
 *
 * A supplement to CheckoutManagementInterface with alternative methods that don't rely on an active
 * customer session.
 *
 * @see CheckoutManagementInterface
 *
 * @api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface GuestCheckoutManagementInterface
{
    /**
     * Persist a set of customer shipping option selections for a masked cart id.
     *
     * @param string $cartId
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface[] $shippingOptionSelections
     * @return void
     */
    public function updateShippingOptionSelections(string $cartId, array $shippingOptionSelections);
}
