<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Util;

/**
 * Interface UnitConverterInterface
 *
 * @package Dhl\ShippingCore\Util
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link http://www.netresearch.de/
 */
interface UnitConverterInterface
{
    const CONVERSION_PRECISION = 3;

    /**
     * @param float  $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertDimension(float $value, string $unitIn, string $unitOut): float;

    /**
     * @param float  $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertMonetaryValue(float $value, string $unitIn, string $unitOut): float;

    /**
     * @param float  $value
     * @param string $unitIn
     * @param string $unitOut
     *
     * @return float
     */
    public function convertWeight(float $value, string $unitIn, string $unitOut): float;
}
