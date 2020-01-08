<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;

/**
 * Interface CheckoutManagementInterface
 *
 * A service for exchanging data for DHL Shipping methods during the checkout process.
 *
 * Use this service to retrieve any shipping options and other checkout data,
 * as well as to persist customer selections for shipping options.
 *
 * @api
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface CheckoutManagementInterface
{
    /**
     * Retrieve the currently configured checkout data concerning the display of additional shipping options
     *
     * @param string $countryId
     * @param string $postalCode
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface
     */
    public function getCheckoutData(string $countryId, string $postalCode): ShippingDataInterface;

    /**
     * Persist a set of customer shipping option selections.
     *
     * @param int $cartId
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\Selection\SelectionInterface[] $shippingOptionSelections
     * @return void
     */
    public function updateShippingOptionSelections(int $cartId, array $shippingOptionSelections);
}
