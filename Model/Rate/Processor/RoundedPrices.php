<?php
/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Rate\Processor;

use Dhl\ShippingCore\Model\Config\RateConfigInterface;
use Dhl\ShippingCore\Model\Config\Source\RoundedPricesFormat;
use Dhl\ShippingCore\Model\Rate\RateProcessorInterface;
use Magento\Quote\Model\Quote\Address\RateRequest;

/**
 * A rate processor to round prices.
 *
 * @package  Dhl\ShippingCore\Model
 * @author   Ronny Gertler <ronny.gertler@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class RoundedPrices implements RateProcessorInterface
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
     * @inheritdoc
     */
    public function processMethods(array $methods, RateRequest $request = null, $carrierCode = null): array
    {
        foreach ($methods as $method) {
            $method->setPrice(
                $this->roundPrice($carrierCode, $method->getPrice())
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
