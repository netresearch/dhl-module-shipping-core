<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentResponse;

use Dhl\ShippingCore\Api\Data\ShipmentResponse\LabelResponseInterface;
use Magento\Framework\DataObject;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * LabelResponse
 *
 * The response type consumed by the core carrier to persist label binary and tracking number.
 *
 * @see \Magento\Shipping\Model\Carrier\AbstractCarrierOnline::requestToShipment
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class LabelResponse extends DataObject implements LabelResponseInterface
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
     * Get tracking number from response.
     *
     * @return string
     */
    public function getTrackingNumber(): string
    {
        return $this->getData(self::TRACKING_NUMBER);
    }

    /**
     * Get label binary from response.
     *
     * @return string
     */
    public function getShippingLabelContent(): string
    {
        return $this->getData(self::SHIPPING_LABEL_CONTENT);
    }

    /**
     * @return ShipmentInterface
     */
    public function getSalesShipment(): ShipmentInterface
    {
        return $this->getData(self::SALES_SHIPMENT);
    }
}
