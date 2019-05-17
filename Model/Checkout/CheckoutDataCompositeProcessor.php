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
class CheckoutDataCompositeProcessor implements InternalProcessorInterface
{
    /**
     * @var CheckoutProcessorInterface[]
     */
    private $processors;

    /**
     * @var InternalProcessorInterface[]
     */
    private $internalProcessors;

    /**
     * CheckoutDataCompositeProcessor constructor.
     *
     * @param CheckoutProcessorInterface[] $processors
     * @param InternalProcessorInterface[] $internalProcessors
     */
    public function __construct(array $processors = [], array $internalProcessors = [])
    {
        $this->processors = $processors;
        $this->internalProcessors = $internalProcessors;
    }

    /**
     * Process $checkoutData according to internal Processors (applied automatically across all carriers) and
     * carrier-specific Processors (applied only to the carrier specified by the processor).
     *
     * @param array $checkoutData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return array
     */
    public function process(array $checkoutData, string $countryId, string $postalCode, int $scopeId = null): array
    {
        foreach ($this->internalProcessors as $processor) {
            $checkoutData = $processor->process($checkoutData, $countryId, $postalCode, $scopeId);
        }

        foreach ($this->processors as $processor) {
            $carrierCode = $processor->getCarrier();
            array_map(static function ($carrierData) use ($processor, $carrierCode, $countryId, $postalCode, $scopeId) {
                if ($carrierData['carrierCode'] === $carrierCode) {
                    $carrierData = $processor->process(
                        $carrierData,
                        $countryId,
                        $postalCode,
                        $scopeId
                    );
                }

                return $carrierData;
            }, $checkoutData);
        }

        return $checkoutData;
    }
}
