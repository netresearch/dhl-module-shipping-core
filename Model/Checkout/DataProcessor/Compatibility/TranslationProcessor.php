<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor\Compatibility;

use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\CompatibilityProcessorInterface;

/**
 * Class TranslationProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class TranslationProcessor implements CompatibilityProcessorInterface
{
    /**
     * @param CompatibilityInterface[] $compatibilityData
     *
     * @return CompatibilityInterface[]
     */
    public function process(array $compatibilityData): array
    {
        foreach ($compatibilityData as $compatibility) {
            $compatibility->setErrorMessage(
                __($compatibility->getErrorMessage())->render()
            );
        }

        return $compatibilityData;
    }
}
