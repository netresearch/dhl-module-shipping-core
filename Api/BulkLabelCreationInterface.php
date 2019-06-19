<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\ShipmentResponse\ShipmentResponseInterface;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Interface BulkLabelCreationInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface BulkLabelCreationInterface
{
    /**
     * @param Request[] $shipmentRequests
     * @return ShipmentResponseInterface[]
     */
    public function createLabels(array $shipmentRequests): array;
}
