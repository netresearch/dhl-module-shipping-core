<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Util;

use Dhl\ShippingCore\Api\UnitConverterInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\Locale\FormatInterface;
use Magento\Shipping\Helper\Carrier;

/**
 * UnitConverter
 *
 * @package  Dhl\ShippingCore\Util
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class UnitConverter implements UnitConverterInterface
{
    /**
     * @var FormatInterface
     */
    private $localeFormat;

    /**
     * @var Data
     */
    private $currencyConverter;

    /**
     * @var Carrier
     */
    private $unitConverter;

    /**
     * UnitConverter constructor.
     * @param FormatInterface $localeFormat
     * @param Data $currencyConverter
     * @param Carrier $unitConverter
     */
    public function __construct(
        FormatInterface $localeFormat,
        Data $currencyConverter,
        Carrier $unitConverter
    ) {
        $this->localeFormat = $localeFormat;
        $this->currencyConverter = $currencyConverter;
        $this->unitConverter = $unitConverter;
    }

    /**
     * Convert dimension from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     * @return float
     */
    public function convertDimension(float $value, string $unitIn, string $unitOut): float
    {
        $localFormatValue = (float) $this->localeFormat->getNumber($value);
        $converted = (float) $this->unitConverter->convertMeasureDimension($localFormatValue, $unitIn, $unitOut);

        return round($converted, self::CONVERSION_PRECISION);
    }

    /**
     * Convert monetary value from one currency into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     * @return float
     */
    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float
    {
        $amount = $this->currencyConverter->currencyConvert($value, $unitIn, $unitOut);
        return round($amount, self::CONVERSION_PRECISION);
    }

    /**
     * Convert weight from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     * @return float
     */
    public function convertWeight(float $value, string $unitIn, string $unitOut): float
    {
        $value = (float) $this->localeFormat->getNumber($value);
        if ($value === 0.0) {
            return $value;
        }

        $converted = (float) $this->unitConverter->convertMeasureWeight($value, $unitIn, $unitOut);
        return round($converted, self::CONVERSION_PRECISION);
    }
}
