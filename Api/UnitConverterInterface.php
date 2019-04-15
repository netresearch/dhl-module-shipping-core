<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

/**
 * Interface UnitConverterInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
interface UnitConverterInterface
{
    const CONVERSION_PRECISION = 3;

    /**
     * Convert dimension from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertDimension(float $value, string $unitIn, string $unitOut): float;

    /**
     * Convert monetary value from one currency into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float;

    /**
     * Convert weight from one unit of measurement into another.
     *
     * @param float $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertWeight(float $value, string $unitIn, string $unitOut): float;
}
