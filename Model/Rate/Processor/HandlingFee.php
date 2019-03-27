<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Rate\RateProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * A rate processor to append the handling fee based on handling type to the shipping price.
 *
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @link     http://www.netresearch.de/
 */
class HandlingFee implements RateProcessorInterface
{
    /**
     * @var RateConfigInterface
     */
    private $rateConfig;

    /**
     * HandlingFee constructor.
     *
     * @param RateConfigInterface $rateConfig
     */
    public function __construct(RateConfigInterface $rateConfig)
    {
        $this->rateConfig = $rateConfig;
    }

    /**
     * @inheritdoc
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        /** @var Method $method */
        foreach ($methods as $method) {
            $carrierCode = $method->getData('carrier');

            // Calculate fee depending on shipping type
            $price = $this->calculatePrice(
                $method->getData('price'),
                $this->getHandlingType($carrierCode, $request),
                $this->getHandlingFee($carrierCode, $request)
            );

            $method->setPrice($price);
            $method->setData('cost', $price);
        }

        return $methods;
    }

    /**
     * Calculates the shipping price altered by the handling type aqnd fee.
     *
     * @param float $amount The total price of the rated shipment for the product
     * @param string $handlingType The handling type determining the type of calculation to do
     * @param float $handlingFee The handling fee to apply to the amount
     *
     * @return float
     */
    private function calculatePrice(float $amount, string $handlingType, float $handlingFee): float
    {
        if ($handlingType === AbstractCarrier::HANDLING_TYPE_PERCENT) {
            $amount += $amount * $handlingFee / 100.0;
        } elseif ($handlingType === AbstractCarrier::HANDLING_TYPE_FIXED) {
            $amount += $handlingFee;
        }

        return max(0.0, $amount);
    }

    /**
     * Returns TRUE, if the current rate request is determined for a cross border route.
     *
     * @param null|RateRequest $request The rate request
     *
     * @return bool
     */
    private function isCrossBorderRoute(RateRequest $request = null): bool
    {
        return $request ? ($request->getDestCountryId() !== $request->getOrigCountryId()) : false;
    }

    /**
     * Returns the configured handling type depending on the shipping type.
     *
     * @param string           $carrierCode The carrier code
     * @param null|RateRequest $request     The rate request
     *
     * @return string
     */
    private function getHandlingType(string $carrierCode, RateRequest $request = null): string
    {
        if ($this->isCrossBorderRoute($request)) {
            return $this->rateConfig->getInternationalHandlingType($carrierCode);
        }

        return $this->rateConfig->getDomesticHandlingType($carrierCode);
    }

    /**
     * Returns the configured handling fee depending on the shipping type.
     *
     * @param string           $carrierCode The carrier code
     * @param null|RateRequest $request     The rate request
     *
     * @return float
     */
    private function getHandlingFee(string $carrierCode, RateRequest $request = null): float
    {
        if ($this->isCrossBorderRoute($request)) {
            return $this->rateConfig->getInternationalHandlingFee($carrierCode);
        }

        return $this->rateConfig->getDomesticHandlingFee($carrierCode);
    }
}
