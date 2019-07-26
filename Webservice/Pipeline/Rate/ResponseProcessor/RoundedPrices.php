<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Webservice\Pipeline\Rate\ResponseProcessor;

use Dhl\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;
use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Config\Source\RoundedPricesFormat;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;

/**
 * A rate processor to round prices.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Ronny Gertler <ronny.gertler@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class RoundedPrices implements RateResponseProcessorInterface
{
    /**
     * @var RateConfigInterface
     */
    private $rateConfig;

    /**
     * RoundedPrices constructor.
     *
     * @param RateConfigInterface $rateConfig
     */
    public function __construct(RateConfigInterface $rateConfig)
    {
        $this->rateConfig = $rateConfig;
    }

    /**
     * Round shipping price according to module configuration rules.
     *
     * @param Method[] $methods List of rate methods
     * @param RateRequest|null $request The rate request
     *
     * @return Method[]
     */
    public function processMethods(array $methods, RateRequest $request = null): array
    {
        foreach ($methods as $method) {
            $method->setPrice(
                $this->roundPrice($method->getData('carrier'), $method->getPrice())
            );
        }

        return $methods;
    }

    /**
     * Round a given price on the basis of the internal module configuration.
     *
     * @param string $carrierCode
     * @param float $price
     * @return float
     */
    private function roundPrice($carrierCode, float $price): float
    {
        $format = $this->rateConfig->getRoundedPricesFormat($carrierCode);

        // Do not round
        if ($format === RoundedPricesFormat::DO_NOT_ROUND) {
            return $price;
        }

        // Price should be rounded to a given decimal value
        if ($format === RoundedPricesFormat::STATIC_DECIMAL) {
            if ($this->rateConfig->roundUp($carrierCode)) {
                $roundedPrice = $this->roundUpToStaticDecimal($carrierCode, $price);
            } else {
                $roundedPrice = $this->roundOffToStaticDecimal($carrierCode, $price);
            }
            return $roundedPrice;
        }

        // Price should be rounded to the next integral number.
        return $this->rateConfig->roundUp($carrierCode) ? ceil($price) : floor($price);
    }

    /**
     * Round given price down to a configured decimal value.
     *
     * @param string $carrierCode
     * @param float $price
     * @return float
     */
    private function roundOffToStaticDecimal($carrierCode, float $price): float
    {
        $roundedDecimal = $this->rateConfig->getRoundedPricesStaticDecimal($carrierCode);
        $decimal = $price - floor($price);

        if ($decimal === $roundedDecimal) {
            return $price;
        }

        if ($decimal < $roundedDecimal) {
            $roundedPrice = floor($price) - 1 + $roundedDecimal;
            return $roundedPrice < 0 ? 0 : floor($price) - 1 + $roundedDecimal;
        }

        return floor($price) + $roundedDecimal;
    }

    /**
     * Round given price up to a configured decimal value.
     *
     * @param string $carrierCode
     * @param float $price
     * @return float
     */
    private function roundUpToStaticDecimal($carrierCode, float $price): float
    {

        $roundedDecimal = $this->rateConfig->getRoundedPricesStaticDecimal($carrierCode);
        $decimal = $price - floor($price);

        if ($decimal === $roundedDecimal) {
            return $price;
        }

        if ($decimal < $roundedDecimal) {
            return floor($price) + $roundedDecimal;
        }

        return ceil($price) + $roundedDecimal;
    }
}
