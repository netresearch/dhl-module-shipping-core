<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Shipping\Model\Shipment\Request;

/**
 * Interface RequestModifierInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface RequestModifierInterface
{
    /**
     * @param Request $shipmentRequest
     * @return Request
     */
    public function modify(Request $shipmentRequest): Request;
}
