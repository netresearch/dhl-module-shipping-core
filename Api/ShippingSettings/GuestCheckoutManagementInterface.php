<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings;

/**
 * A supplement to CheckoutManagementInterface with alternative methods that don't rely on an active customer session.
 *
 * @see CheckoutManagementInterface
 *
 * @api
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
