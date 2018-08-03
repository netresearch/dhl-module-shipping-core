<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Method;

use Dhl\ShippingCore\Api\Data\MethodAdditionalInfoInterface;
use Magento\Framework\DataObject;

/**
 * Class AdditionalInfo
 *
 * @package Dhl\ShippingCore\Model\Method
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
final class AdditionalInfo extends DataObject implements MethodAdditionalInfoInterface
{

    /**
     * @inheritdoc
     */
    public function getDeliveryDate(): string
    {
        return (string)$this->getData(self::DELIVERY_DATE);
    }

    /**
     * @inheritdoc
     */
    public function setDeliveryDate(string $deliveryDate): void
    {
        $this->setData(self::DELIVERY_DATE, $deliveryDate);
    }

    /**
     * @inheritdoc
     */
    public function getCarrierLogoUrl(): string
    {
        return (string)$this->getData(self::CARRIER_LOGO_URL);
    }

    /**
     * @inheritdoc
     */
    public function setCarrierLogoUrl(string $carrierLogoUrl): void
    {
        $this->setData(self::CARRIER_LOGO_URL, $carrierLogoUrl);
    }

}