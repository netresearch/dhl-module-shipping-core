<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

/**
 * Wrapper around the Magento Shipping module's shipping settings configuration.
 *
 * @api
 */
interface ShippingConfigInterface
{
    /**
     * Returns the shipping origin country code.
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginCountry($store = null): string;

    /**
     * Returns the shipping origin region ID.
     *
     * @param mixed $store
     * @return int
     */
    public function getOriginRegion($store = null): int;

    /**
     * Returns the shipping origin city.
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginCity($store = null): string;

    /**
     * Returns the shipping origin postal code.
     *
     * @param mixed $store
     * @return string
     */
    public function getOriginPostcode($store = null): string;

    /**
     * Returns the shipping origin street.
     *
     * @param mixed $store
     * @return string[]
     */
    public function getOriginStreet($store = null): array;
}
