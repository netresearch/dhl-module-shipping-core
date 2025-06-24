<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate\ResponseProcessor;

use Dhl\ShippingCore\Model\Config\RateConfig;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Carrier\AbstractCarrier;
use Netresearch\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;

/**
 * Add markup to shipping rates.
 *
 * Shipping rates coming from a Products & Rates API may not be suitable for the consumer.
 * To get from the shipping cost to a shipping price, markup may be applied. The markup
 * configuration is defined in `500_rates_calculation.xml` config template and can be
 * accessed via the RateConfig model.
 */
class HandlingFee implements RateResponseProcessorInterface
{
    /**
     * @var RateConfig
     */
    private $rateConfig;

    public function __construct(RateConfig $rateConfig)
    {
        $this->rateConfig = $rateConfig;
    }

    /**
     * Calculate markup for the given amount.
     *
     * @param float $price
     * @param string $carrierCode
     * @return float
     */
    private function calculateDomesticMarkup(float $price, string $carrierCode, mixed $store = null): float
    {
        $markupType = $this->rateConfig->getDomesticMarkupType($carrierCode, $store);

        if ($markupType === AbstractCarrier::HANDLING_TYPE_FIXED) {
            return (float) $this->rateConfig->getDomesticMarkupAmount($carrierCode, $store);
        } elseif ($markupType === AbstractCarrier::HANDLING_TYPE_PERCENT) {
            $percentage = $this->rateConfig->getDomesticMarkupPercentage($carrierCode, $store);
            return $price * ($percentage / 100);
        } else {
            return 0;
        }
    }

    /**
     * Calculate markup for the given amount.
     *
     * @param float $price
     * @param string $carrierCode
     * @return float
     */
    private function calculateInternationalMarkup(float $price, string $carrierCode, mixed $store = null): float
    {
        $markupType = $this->rateConfig->getInternationalMarkupType($carrierCode, $store);

        if ($markupType === AbstractCarrier::HANDLING_TYPE_FIXED) {
            return (float) $this->rateConfig->getInternationalMarkupAmount($carrierCode, $store);
        } elseif ($markupType === AbstractCarrier::HANDLING_TYPE_PERCENT) {
            $percentage = $this->rateConfig->getInternationalMarkupPercentage($carrierCode, $store);
            return $price * ($percentage / 100);
        } else {
            return 0;
        }
    }

    /**
     * Add handling fee to shipping price if applicable.
     *
     * @param Method[] $methods List of rate methods
     * @param RateRequest|null $request The rate request
     *
     * @return Method[]
     */
    #[\Override]
    public function processMethods(array $methods, ?RateRequest $request = null): array
    {
        if (!$request instanceof RateRequest) {
            return $methods;
        }

        $store = $request->getStoreId();
        $isDomestic = $request->getDestCountryId() === $request->getData('country_id');

        foreach ($methods as $method) {
            $carrierCode = $method->getData('carrier');

            $isMarkupEnabled = $isDomestic
                ? $this->rateConfig->isDomesticMarkupEnabled($carrierCode, $store)
                : $this->rateConfig->isInternationalMarkupEnabled($carrierCode, $store);

            if (!$isMarkupEnabled) {
                continue;
            }

            $markup = $isDomestic
                ? $this->calculateDomesticMarkup($method->getData('price'), $carrierCode, $store)
                : $this->calculateInternationalMarkup($method->getData('price'), $carrierCode, $store);

            $price = max(0.0, $method->getData('price') + $markup);

            $method->setPrice($price);
        }

        return $methods;
    }
}
