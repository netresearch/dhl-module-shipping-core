<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingOptions\CheckoutProcessorInterface;

/**
 * Class CheckoutDataCompositeProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class CheckoutDataCompositeProcessor implements CheckoutProcessorInterface
{
    /**
     * @var CheckoutProcessorInterface[]
     */
    private $processors;

    /**
     * CheckoutDataCompositeProcessor constructor.
     *
     * @param CheckoutProcessorInterface[] $processors
     */
    public function __construct(array $processors = [])
    {
        $this->processors = $processors;
    }

    /**
     * @param array $optionsData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($this->processors as $processor) {
            $optionsData = $processor->processShippingOptions(
                $optionsData,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $optionsData;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): MetadataInterface {
        foreach ($this->processors as $processor) {
            $metadata = $processor->processMetadata(
                $metadata,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $metadata;
    }

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return CompatibilityInterface[]
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($this->processors as $processor) {
            $compatibilityData = $processor->processCompatibilityData(
                $compatibilityData,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $compatibilityData;
    }
}
