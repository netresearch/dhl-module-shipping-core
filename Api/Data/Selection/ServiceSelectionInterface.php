<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface ServiceSelectionInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface ServiceSelectionInterface
{
    const SERVICE_CODE = 'service_code';

    const INPUT_CODE = 'input_code';

    const VALUE = 'service_value';

    /**
     * Get the service code, e.g. "packstation".
     *
     * @return string
     */
    public function getServiceCode(): string;

    /**
     * Get the input code, e.g. "packstationNumber" or "postNumber".
     *
     * @return string
     */
    public function getInputCode(): string;

    /**
     * Get the service value, e.g. 520 (packstationNumber) or 12345678 (postNumber).
     *
     * @return string
     */
    public function getValue(): string;
}
