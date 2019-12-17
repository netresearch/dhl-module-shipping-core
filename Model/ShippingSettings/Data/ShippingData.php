<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;

/**
 * Class ShippingData
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShippingData implements ShippingDataInterface
{
    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[]
     */
    private $carriers;

    /**
     * ShippingData constructor.
     *
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[] $carriers
     */
    public function __construct(array $carriers = [])
    {
        $this->carriers = $carriers;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[]
     */
    public function getCarriers(): array
    {
        return $this->carriers;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface[] $carriers
     */
    public function setCarriers(array $carriers)
    {
        $this->carriers = $carriers;
    }
}
