<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Magento\Framework\App\ObjectManager;

/**
 * Class CheckoutDataFactory
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataFactory
{
    /**
     * @var ObjectManager
     */
    private $objectManager;

    /**
     * CheckoutDataFactory constructor.
     *
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    /**
     * Create an Instance of CheckoutDataInterface, explicitly accepting CarrierDataInterface[] as parameter.
     *
     * This method is also indirectly inspected using Reflection
     * by \Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider.
     *
     * @param CarrierDataInterface[] $carrierData
     * @return CheckoutDataInterface
     */
    public function create(array $carrierData): CheckoutDataInterface
    {
        return $this->objectManager->create(
            CheckoutDataInterface::class,
            ['carriers' => $carrierData]
        );
    }
}
