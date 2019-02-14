<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface ServiceInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface ServiceInterface
{
    /**
     * Obtain service code.
     *
     * @return string
     */
    public function getCode(): string;

    /**
     * Obtain service name.
     *
     * @return string
     */
    public function getLabel(): string;

    /**
     * Check if service is enabled for display.
     *
     * @return bool
     */
    public function isEnabledForCheckout(): bool;

    /**
     * Check if service can be selected by customer.
     *
     * @return bool
     */
    public function isEnabledForPackaging(): bool;

    /**
     * Check if service can be booked during autocreate (cron or mass action).
     *
     * @return bool
     */
    public function isEnabledForAutocreate(): bool;

    /**
     * Check if service can be modified by merchant.
     *
     * @return bool
     */
    public function isPackagingReadonly(): bool;

    /**
     * Obtain a list of inputs for displaying the service and it's values
     *
     * @return \Dhl\ShippingCore\Api\Data\Service\InputInterface[]
     */
    public function getInputs(): array;

    /**
     * Check if the service can be booked with postal facility deliveries.
     *
     * @return bool
     */
    public function isAvailableAtPostalFacility(): bool;

    /**
     * Obtain routes the service can be booked with.
     *
     * @return string[][]
     */
    public function getRoutes(): array;

    /**
     * @return int
     */
    public function getSortOrder(): int;
}
