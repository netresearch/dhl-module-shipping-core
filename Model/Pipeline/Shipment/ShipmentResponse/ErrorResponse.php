<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment\ShipmentResponse;

use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse\ShipmentErrorResponseInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Phrase;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * ErrorResponse
 *
 * The response type consumed by the core carrier to identify errors during the shipment request.
 *
 * @see \Magento\Shipping\Model\Carrier\AbstractCarrierOnline::requestToShipment
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ErrorResponse extends DataObject implements ShipmentErrorResponseInterface
{
    /**
     * Obtain request id (package id, sequence number).
     *
     * @return string
     */
    public function getRequestIndex(): string
    {
        return $this->getData(self::REQUEST_INDEX);
    }

    /**
     * Obtain shipment the label was requested for.
     *
     * @return ShipmentInterface
     */
    public function getSalesShipment(): ShipmentInterface
    {
        return $this->getData(self::SALES_SHIPMENT);
    }

    /**
     * Get errors from response.
     *
     * @return Phrase
     */
    public function getErrors(): Phrase
    {
        return $this->getData(self::ERRORS);
    }
}
