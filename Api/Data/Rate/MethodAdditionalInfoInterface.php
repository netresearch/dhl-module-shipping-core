<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Rate;

/**
 * @api
 */
interface MethodAdditionalInfoInterface
{
    const ATTRIBUTE_KEY = 'additional_info';
    const DELIVERY_DATE = 'delivery_date';
    const CARRIER_LOGO_URL = 'carrier_logo_url';

    /**
     * @return string
     */
    public function getDeliveryDate(): string;

    /**
     * @param string $deliveryDate
     * @return void
     */
    public function setDeliveryDate(string $deliveryDate);

    /**
     * @return string
     */
    public function getCarrierLogoUrl(): string;

    /**
     * @param string $carrierLogoUrl
     * @return void
     */
    public function setCarrierLogoUrl(string $carrierLogoUrl);
}
