<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Api\CheckoutManagementInterface;
use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingDataInterfaceFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Framework\Webapi\ServiceOutputProcessor;

/**
 * Class ShippingDataHydrator
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ShippingDataHydrator
{
    /**
     * @var ShippingDataInterfaceFactory
     */
    private $shippingDataFactory;

    /**
     * @var ServiceInputProcessor
     */
    private $inputProcessor;

    /**
     * @var ServiceOutputProcessor
     */
    private $outputProcessor;

    /**
     * ShippingDataHydrator constructor.
     *
     * @param ShippingDataInterfaceFactory $shippingDataFactory
     * @param ServiceInputProcessor $inputProcessor
     * @param ServiceOutputProcessor $outputProcessor
     */
    public function __construct(
        ShippingDataInterfaceFactory $shippingDataFactory,
        ServiceInputProcessor $inputProcessor,
        ServiceOutputProcessor $outputProcessor
    ) {
        $this->shippingDataFactory = $shippingDataFactory;
        $this->inputProcessor = $inputProcessor;
        $this->outputProcessor = $outputProcessor;
    }

    /**
     * Convert a plain nested array of scalar types into a ShippingDataInterface object.
     *
     * @param array $data
     * @return ShippingDataInterface
     * @throws LocalizedException
     */
    public function toObject(array $data): ShippingDataInterface
    {
        try {
            $carrierData = $this->inputProcessor->process(
                ShippingDataInterface::class,
                'setCarriers',
                $data
            );

            return $this->shippingDataFactory->create(['carriers' => array_shift($carrierData)]);
        } catch (\Exception $exception) {
            throw new LocalizedException(__('ShippingData object generation failed. Check input data array.'));
        }
    }

    /**
     * Convert a ShippingDataInterface object into a plain nested array of scalar types.
     *
     * @param ShippingDataInterface $data
     * @return array
     */
    public function toArray(ShippingDataInterface $data): array
    {
        return $this->outputProcessor->process(
            $data,
            CheckoutManagementInterface::class,
            'getCheckoutData'
        );
    }
}
