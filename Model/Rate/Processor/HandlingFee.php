<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\Express\Api\Data\ShippingProductsInterface;
use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Rate\RateProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Magento\Shipping\Model\Carrier\AbstractCarrier;

/**
 * A rate processor to append the handling fee based on handling type to the shipping price.
 *
 * @package  Dhl\Express\Model
 * @author   Rico Sonntag <rico.sonntag@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
    public function processMethods(array $methods, RateRequest $request = null, $carrierCode = null): array
    {
        /** @var Method $method */
        foreach ($methods as $method) {
            // Calculate fee depending on shipping type
            $price = $this->calculatePrice(
                $method->getPrice(),
                $this->getHandlingType($carrierCode, $method),
                $this->getHandlingFee($carrierCode, $method)
            );

            $method->setPrice($price);
            $method->setCost($price);
        }

        return $methods;
    }

    /**
     * Returns the configured handling type depending on the shipping type.
     *
     * @param string $carrierCode
     * @param Method $method The rate method
     *
     * @return string
     */
    private function getHandlingType($carrierCode, Method $method): string
    {
        // Calculate fee depending on shipping type
        if ($this->isDomesticShipping($method)) {
            return $this->rateConfig->getDomesticHandlingType($carrierCode);
        }

        return $this->rateConfig->getInternationalHandlingType($carrierCode);
    }

    /**
     * Returns the configured handling fee depending on the shipping type.
     *
     * @param string $carrierCode
     * @param Method $method The rate method
     *
     * @return float
     */
    private function getHandlingFee($carrierCode, Method $method): float
    {
        // Calculate fee depending on shipping type
        if ($this->isDomesticShipping($method)) {
            return $this->rateConfig->getDomesticHandlingFee($carrierCode);
        }

        return $this->rateConfig->getInternationalHandlingFee($carrierCode);
    }

    /**
     * Returns whether the given method applies to domestic shipping or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    private function isDomesticShipping(Method $method): bool
    {
        return \in_array($method->getMethod(), ShippingProductsInterface::PRODUCTS_DOMESTIC, true);
    }

    /**
     * Calculates the shipping price altered by the handling type aqnd fee.
     *
     * @param float  $amount       The total price of the rated shipment for the product
     * @param string $handlingType The handling type determining the type of calculation to do
     * @param float  $handlingFee  The handling fee to apply to the amount
     *
     * @return float
     */
    private function calculatePrice(float $amount, string $handlingType, float $handlingFee): float
    {
        if ($handlingType === AbstractCarrier::HANDLING_TYPE_PERCENT) {
            $amount += $amount * $handlingFee / 100.0;
        } else {
            $amount += $handlingFee;
        }

        return $amount < 0.0 ? 0.0 : $amount;
    }
}
