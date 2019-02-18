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
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        /** @var Method $method */
        foreach ($methods as $method) {
            // Calculate fee depending on shipping type
            $price = $this->calculatePrice(
                $method->getPrice(),
                $this->getHandlingType($method),
                $this->getHandlingFee($method)
            );

            $method->setPrice($price);
            $method->setCost($price);
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
     * Returns the configured handling type depending on the shipping type.
     *
     * @param Method $method The rate method
     *
     * @return string
     */
    private function getHandlingType(Method $method): string
    {
        // Calculate fee depending on shipping type
        if ($this->isEnabledDomesticProduct($method)) {
            return $this->rateConfig->getDomesticHandlingType($method->getData('carrier'));
        }

        if ($this->isEnabledInternationalProduct($method)) {
            return $this->rateConfig->getInternationalHandlingType($method->getData('carrier'));
        }

        return '';
    }

    /**
     * Returns whether the product is enabled in the configuration or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    protected function isEnabledDomesticProduct(Method $method): bool
    {
        return \in_array(
            $method->getData('method'),
            $this->rateConfig->getAllowedDomesticProducts($method->getData('carrier')),
            true
        );
    }

    /**
     * Returns whether the product is enabled in the configuration or not.
     *
     * @param Method $method The rate method
     *
     * @return bool
     */
    protected function isEnabledInternationalProduct(Method $method): bool
    {
        return \in_array(
            $method->getData('method'),
            $this->rateConfig->getAllowedInternationalProducts($method->getData('carrier')),
            true
        );
    }

    /**
     * Returns the configured handling fee depending on the shipping type.
     *
     * @param Method $method The rate method
     *
     * @return float
     */
    private function getHandlingFee(Method $method): float
    {
        $handlingFee = 0.0;
        // Calculate fee depending on shipping type
        if ($this->isEnabledDomesticProduct($method)) {
            $handlingFee = $this->rateConfig->getDomesticHandlingFee($method->getData('carrier'));
        } elseif ($this->isEnabledInternationalProduct($method)) {
            $handlingFee = $this->rateConfig->getInternationalHandlingFee($method->getData('carrier'));
        }

        return $handlingFee;
    }
}
