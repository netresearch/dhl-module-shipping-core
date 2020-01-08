<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Compatibility;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\CompatibilityProcessorInterface;

/**
 * Class TranslationProcessor
 *
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
