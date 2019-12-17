<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;

/**
 * Class ShippingOptionsProcessorInterface
 *
 * @api
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface ShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option items and modify them according to business logic.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryCode Destination country code
     * @param string $postalCode Destination postal code
     * @param int|null $storeId
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $optionsData,
        string $countryCode,
        string $postalCode,
        int $storeId = null
    ): array;
}
