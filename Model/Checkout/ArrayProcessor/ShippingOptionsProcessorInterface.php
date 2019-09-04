<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\ArrayProcessor;

/**
 * Class ShippingOptionsProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Checkout\ArrayProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface ShippingOptionsProcessorInterface
{
    /**
     * Receive an array of shipping option data and
     * modify it according to business logic.
     *
     * @param mixed[] $shippingData
     * @param int $storeId
     *
     * @return mixed[]
     */
    public function process(array $shippingData, int $storeId): array;
}
