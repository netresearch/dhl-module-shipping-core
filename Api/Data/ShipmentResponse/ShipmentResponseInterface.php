<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShipmentResponse;

use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Interface ShipmentResponseInterface
 *
 * @api
 */
interface ShipmentResponseInterface
{
    const REQUEST_INDEX = 'request_index';
    const SALES_SHIPMENT = 'sales_shipment';

    /**
     * Obtain request index (unique package id, sequence number).
     *
     * @return string
     */
    public function getRequestIndex(): string;

    /**
     * @return ShipmentInterface
     */
    public function getSalesShipment(): ShipmentInterface;
}
