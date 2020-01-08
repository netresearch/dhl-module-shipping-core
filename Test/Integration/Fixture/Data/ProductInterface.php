<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Test\Integration\Fixture\Data;

/**
 * Interface ProductInterface
 *
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
interface ProductInterface
{
    /**
     * @return string
     */
    public function getType(): string;

    /**
     * @return string
     */
    public function getSku(): string;

    /**
     * @return float
     */
    public function getPrice(): float;

    /**
     * @return float
     */
    public function getWeight(): float;

    /**
     * @return string[]
     */
    public function getCustomAttributes(): array;

    /**
     * @return int
     */
    public function getCheckoutQty(): int;

    /**
     * @return string
     */
    public function getDescription(): string;
}
