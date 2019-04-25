<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;

/**
 * Class CheckoutData
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CheckoutData implements CheckoutDataInterface
{
    /**
     * @var \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[]
     */
    private $carriers = [];

    /**
     * CheckoutData constructor.
     *
     * @param \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[] $carriers
     */
    public function __construct(array $carriers = [])
    {
        $this->carriers = $carriers;
    }

    /**
     * @return CarrierDataInterface[]
     */
    public function getCarriers(): array
    {
        return $this->carriers;
    }
}
