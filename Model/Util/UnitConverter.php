<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Dhl\ShippingCore\Api\Util\UnitConverterInterface;
use Magento\Directory\Helper\Data;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Locale\FormatInterface;
use Magento\Shipping\Helper\Carrier;

/**
 * UnitConverter
 *
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
     *
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
     * @throws NoSuchEntityException
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

    /**
     * Returns lowercase two letter representation of weight unit, e.g. KILOGRAM => kg
     *
     * @param $weightUnit
     * @return string
     */
    public function normalizeWeightUnit($weightUnit): string
    {
        switch (strtoupper($weightUnit)) {
            case \Zend_Measure_Weight::KILOGRAM:
            case 'KGS':
                return 'kg';
            case \Zend_Measure_Weight::POUND:
            case 'LBS':
                return 'lb';
            default:
                return $weightUnit;
        }
    }

    /**
     * Returns lowercase two letter representation of given dimension unit
     *
     * @param $dimensionUnit
     * @return string
     */
    public function normalizeDimensionUnit($dimensionUnit): string
    {
        switch (strtoupper($dimensionUnit)) {
            case \Zend_Measure_Length::CENTIMETER:
                return 'cm';
            case \Zend_Measure_Length::INCH:
                return 'in';
            default:
                return $dimensionUnit;
        }
    }
}
