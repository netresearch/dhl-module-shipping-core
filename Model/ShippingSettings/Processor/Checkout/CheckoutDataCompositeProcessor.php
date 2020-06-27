<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\CompatibilityProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\MetadataProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\GlobalProcessorInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\ShippingOptionsProcessorInterface;

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
     * @var GlobalProcessorInterface[]
     */
    private $globalProcessors;

    /**
     * CheckoutDataCompositeProcessor constructor.
     *
     * @param ShippingOptionsProcessorInterface[] $serviceOptionsProcessors
     * @param MetadataProcessorInterface[] $metadataProcessors
     * @param CompatibilityProcessorInterface[] $compatibilityProcessors
     * @param GlobalProcessorInterface[] $globalProcessors
     */
    public function __construct(
        array $serviceOptionsProcessors = [],
        array $metadataProcessors = [],
        array $compatibilityProcessors = [],
        array $globalProcessors = []
    ) {
        $this->serviceOptionsProcessors = $serviceOptionsProcessors;
        $this->metadataProcessors       = $metadataProcessors;
        $this->compatibilityProcessors  = $compatibilityProcessors;
        $this->globalProcessors         = $globalProcessors;
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
                $metadata = $carrierData->getMetadata();
                if ($metadata) {
                    /** @var MetadataProcessorInterface $processor */
                    $processor->process($metadata, $storeId);
                }
            }

            foreach ($this->compatibilityProcessors as $processor) {
                /** @var CompatibilityProcessorInterface $processor */
                $carrierData->setCompatibilityData(
                    $processor->process(
                        $carrierData->getCompatibilityData()
                    )
                );
            }

            foreach ($this->globalProcessors as $processor) {
                $processor->process($carrierData);
            }
        }

        return $shippingData;
    }
}
