<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Pipeline\ShipmentResponse;

/**
 * @api
 */
interface LabelResponseInterface extends ShipmentResponseInterface
{
    const TRACKING_NUMBER = 'tracking_number';
    const SHIPPING_LABEL_CONTENT = 'shipping_label_content';

    /**
     * @return string
     */
    public function getTrackingNumber(): string;

    /**
     * @return string
     */
    public function getShippingLabelContent(): string;
}
