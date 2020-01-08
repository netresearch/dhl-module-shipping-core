<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;

/**
 * Class CompatibilityProcessorInterface
 *
 * @api
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface CompatibilityProcessorInterface
{
    /**
     * Receive an array of compatibility rule data items and modify them according to business logic.
     *
     * @param CompatibilityInterface[] $compatibilityData
     *
     * @return CompatibilityInterface[]
     */
    public function process(array $compatibilityData): array;
}
