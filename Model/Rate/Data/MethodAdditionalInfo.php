<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rate\Data;

use Dhl\ShippingCore\Api\Data\Rate\MethodAdditionalInfoInterface;
use Magento\Framework\DataObject;

/**
 * Class MethodAdditionalInfo
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class MethodAdditionalInfo extends DataObject implements MethodAdditionalInfoInterface
{
    public function getDeliveryDate(): string
    {
        return (string) $this->getData(self::DELIVERY_DATE);
    }

    public function setDeliveryDate(string $deliveryDate)
    {
        $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    public function getCarrierLogoUrl(): string
    {
        return (string) $this->getData(self::CARRIER_LOGO_URL);
    }

    public function setCarrierLogoUrl(string $carrierLogoUrl)
    {
        $this->setData(self::CARRIER_LOGO_URL, $carrierLogoUrl);
    }
}
