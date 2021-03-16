<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Rate\ResponseProcessor;

use Dhl\ShippingCore\Model\Config\RateConfig;
use Dhl\ShippingCore\Model\Config\Source\RoundingDirection;
use Dhl\ShippingCore\Model\Config\Source\RoundingFormat;
use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Quote\Model\Quote\Address\RateResult\Method;
use Netresearch\ShippingCore\Api\Pipeline\RateResponseProcessorInterface;

/**
 * Round shipping rates.
 *
 * Shipping rates coming from a Products & Rates API may not be suitable for the
 * consumer. Rounding may be applied according to configuration settings defined in
 * the `500_rates_calculation.xml` config template and accessible via the RateConfig model.
 */
class RoundedPrices implements RateResponseProcessorInterface
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
     * Round a given price on the basis of the internal module configuration.
     *
     * @param string $carrierCode
     * @param float $price
     * @param mixed $store
     * @return float
     */
    private function roundPrice(float $price, string $carrierCode, $store = null): float
    {
        $numberFormat = $this->rateConfig->getRoundingFormat($carrierCode, $store);
        $direction = $this->rateConfig->getRoundingDirection($carrierCode, $store);

        if ($numberFormat === RoundingFormat::INTEGER && $direction === RoundingDirection::UP) {
            return ceil($price);
        } elseif ($numberFormat === RoundingFormat::INTEGER && $direction === RoundingDirection::DOWN) {
            return floor($price);
        } elseif ($numberFormat === RoundingFormat::DECIMAL) {
            $integerPart = (int) floor($price);
            $decimalPart = round($price * 100) - round($integerPart * 100);
            $resultDecimal = (int) $this->rateConfig->getRoundingDecimal($carrierCode, $store);

            if (($direction === RoundingDirection::UP) && ($decimalPart > $resultDecimal)) {
                // round up to next integer, add decimal part
                return $integerPart + 1 + $resultDecimal / 100;
            } elseif (($direction === RoundingDirection::DOWN) && ($decimalPart < $resultDecimal)) {
                // round down to previous integer, add decimal part
                return $integerPart - 1 + $resultDecimal / 100;
            } elseif ($decimalPart !== $resultDecimal) {
                // add decimal part to integer
                return $integerPart + $resultDecimal / 100;
            }
        }

        // no rounding necessary or rounding improperly configured
        return $price;
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
        $store = $request ? $request->getStoreId() : null;

        foreach ($methods as $method) {
            $carrierCode = $method->getData('carrier');

            if (!$this->rateConfig->isRoundingEnabled($carrierCode, $store)) {
                continue;
            }

            $price = $this->roundPrice($method->getData('price'), $method->getData('carrier'), $store);
            $method->setData('price', max(0.0, $price));
        }

        return $methods;
    }
}
