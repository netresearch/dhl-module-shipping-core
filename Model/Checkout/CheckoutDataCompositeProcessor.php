<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\CompatibilityProcessorInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\MetadataProcessorInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\ShippingOptionsProcessorInterface;

/**
 * Class CheckoutDataCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataCompositeProcessor
{
    /**
     * @var ShippingOptionsProcessorInterface[]
     */
    private $serviceOptionsProcessors;

    /**
     * @var MetadataProcessorInterface[]
     */
    private $metadataProcessors;

    /**
     * @var CompatibilityProcessorInterface[]
     */
    private $compatibilityProcessors;

    /**
     * CheckoutDataCompositeProcessor constructor.
     *
     * @param ShippingOptionsProcessorInterface[] $serviceOptionsProcessors
     * @param MetadataProcessorInterface[] $metadataProcessors
     * @param CompatibilityProcessorInterface[] $compatibilityProcessors
     */
    public function __construct(
        array $serviceOptionsProcessors = [],
        array $metadataProcessors = [],
        array $compatibilityProcessors = []
    ) {
        $this->serviceOptionsProcessors = $serviceOptionsProcessors;
        $this->metadataProcessors       = $metadataProcessors;
        $this->compatibilityProcessors  = $compatibilityProcessors;
    }

    /**
     * @param ShippingDataInterface $shippingData
     * @param string $countryCode
     * @param string $postalCode
     * @param int|null $storeId
     *
     * @return ShippingDataInterface
     */
    public function process(
        ShippingDataInterface $shippingData,
        string $countryCode,
        string $postalCode,
        int $storeId = null
    ): ShippingDataInterface {
        foreach ($shippingData->getCarriers() as $carrierData) {
            foreach ($this->serviceOptionsProcessors as $processor) {
                /** @var ShippingOptionsProcessorInterface $processor */
                $carrierData->setServiceOptions(
                    $processor->process(
                        $carrierData->getServiceOptions(),
                        $countryCode,
                        $postalCode,
                        $storeId
                    )
                );
            }

            foreach ($this->metadataProcessors as $processor) {
                /** @var MetadataProcessorInterface $processor */
                $processor->process(
                    $carrierData->getMetadata(),
                    $storeId
                );
            }

            foreach ($this->compatibilityProcessors as $processor) {
                /** @var CompatibilityProcessorInterface $processor */
                $carrierData->setCompatibilityData(
                    $processor->process(
                        $carrierData->getCompatibilityData()
                    )
                );
            }
        }

        return $shippingData;
    }
}
