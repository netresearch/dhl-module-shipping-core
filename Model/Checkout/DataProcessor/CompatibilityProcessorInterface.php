<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;

/**
 * Class CompatibilityProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
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
