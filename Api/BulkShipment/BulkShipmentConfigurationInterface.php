<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\BulkShipment;

use Dhl\ShippingCore\Api\RequestModifierInterface;
use Dhl\ShippingCore\Model\BulkShipment\NotImplementedException;

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
     * @throws NotImplementedException
     */
    public function getRequestModifier(): RequestModifierInterface;

    /**
     * Obtain the service that connects to the carrier's label api for creating labels.
     *
     * @return BulkLabelCreationInterface
     * @throws NotImplementedException
     */
    public function getLabelService(): BulkLabelCreationInterface;

    /**
     * Obtain the service that connects to the carrier's label api for cancelling labels.
     *
     * @return BulkLabelCancellationInterface
     * @throws NotImplementedException
     */
    public function getCancellationService(): BulkLabelCancellationInterface;

    /**
     * Check if a carrier allows deleting single tracks of a shipment.
     *
     * @return bool
     */
    public function isSingleTrackDeletionAllowed(): bool;
}
