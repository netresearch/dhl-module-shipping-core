<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\BulkShipment;

use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentResponseInterface;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Service to create shipping labels.
 *
 * @api
 */
interface BulkLabelCreationInterface
{
    /**
     * Create shipping labels for given shipment requests.
     *
     * @param Request[] $shipmentRequests
     * @return ShipmentResponseInterface[]
     */
    public function createLabels(array $shipmentRequests): array;
}
