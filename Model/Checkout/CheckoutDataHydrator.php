<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CheckoutDataInterface;
use Magento\Framework\Exception\InputException;
use Magento\Framework\Webapi\ServiceInputProcessor;

/**
 * Class CheckoutDataHydrator
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataHydrator
{
    /**
     * @var ServiceInputProcessor
     */
    private $inputProcessor;

    /**
     * @var CheckoutDataFactory
     */
    private $checkoutDataFactory;

    /**
     * CheckoutDataProvider constructor.
     *
     * @param ServiceInputProcessor $inputProcessor
     * @param CheckoutDataFactory $checkoutDataFactory
     */
    public function __construct(
        ServiceInputProcessor $inputProcessor,
        CheckoutDataFactory $checkoutDataFactory
    ) {
        $this->inputProcessor = $inputProcessor;
        $this->checkoutDataFactory = $checkoutDataFactory;
    }

    /**
     * Uses the functionality of the Magento 2 REST API to convert a plain nested
     * array of scalar types into a CheckoutDataInterface object.
     *
     * @param array $data
     * @return CheckoutDataInterface
     * @throws InputException
     */
    public function toObject(array $data): CheckoutDataInterface
    {
        try {
            $carrierData = $this->inputProcessor->process(
                CheckoutDataFactory::class,
                'create',
                ['carrierData' => $data]
            );
            /** Unwrap unnecessarily nested array. */
            $carrierData = array_shift($carrierData);

            return $this->checkoutDataFactory->create($carrierData);
        } catch (\Exception $exception) {
            throw new InputException(__('Error: Invalid checkout data input array given.'), $exception);
        }
    }
}
