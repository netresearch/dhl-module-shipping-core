<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

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

    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $result = $optionsData;
        foreach ($this->processors as $processor) {
            $result = $processor->processShippingOptions(
                $result,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $result;
    }

    public function processMetadata(
        array $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $result = $metadata;
        foreach ($this->processors as $processor) {
            $result = $processor->processMetadata(
                $result,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $result;
    }

    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        $result = $compatibilityData;
        foreach ($this->processors as $processor) {
            $result = $processor->processCompatibilityData(
                $result,
                $countryId,
                $postalCode,
                $scopeId
            );
        }

        return $result;
    }
}
