<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

/**
 * Interface BulkShipmentConfigurationInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface BulkShipmentConfigurationInterface
{
    /**
     * Obtain the carrier code which the current configuration applies to.
     *
     * @return string
     */
    public function getCarrierCode(): string;

    /**
     * Obtain the carrier's modifier to add carrier specific data to the shipment request.
     *
     * @return RequestModifierInterface
     */
    public function getRequestModifier(): RequestModifierInterface;

    /**
     * Obtain the service that connects to the carrier's label api.
     *
     * @return BulkLabelCreationInterface
     */
    public function getLabelService(): BulkLabelCreationInterface;

    /**
     * Check if the customer should be notified after auto-creating the shipment (shipment confirmation email).
     *
     * @return bool
     */
    public function notify(): bool;
}
