<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Magento\Framework\ObjectManagerInterface;

/**
 * Class CheckoutDataFactory
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * CheckoutDataFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create an Instance of CheckoutDataInterface, explicitly accepting CarrierDataInterface[] as parameter.
     *
     * This method is also indirectly inspected using Reflection
     * by \Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider.
     *
     * @param \Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface[] $carrierData
     * @return \Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface
     */
    public function create(array $carrierData): CheckoutDataInterface
    {
        return $this->objectManager->create(
            CheckoutDataInterface::class,
            ['carriers' => $carrierData]
        );
    }
}
