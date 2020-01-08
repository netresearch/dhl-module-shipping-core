<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterfaceFactory;
use Dhl\ShippingCore\Api\ShippingSettings\CheckoutManagementInterface;
use Magento\Framework\Webapi\ServiceInputProcessor;
use Magento\Framework\Webapi\ServiceOutputProcessor;

/**
 * Class ShippingDataHydrator
 *
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
     * Note: For M2.2 compatibility, created types must not have constructors with required values. Only populate
     * entities through setters.
     *
     * @param mixed[] $data
     * @return ShippingDataInterface
     * @throws \RuntimeException
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
            throw new \RuntimeException('ShippingData object generation failed.', $exception);
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
