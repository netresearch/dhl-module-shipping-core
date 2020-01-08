<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\ServiceOptions;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\ShippingOptionsProcessorInterface;

/**
 * Class TranslationProcessor
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class TranslationProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryCode
     * @param string $postalCode
     * @param int|null $storeId
     *
     * @return ShippingOptionInterface[]
     */
    public function process(
        array $optionsData,
        string $countryCode,
        string $postalCode,
        int $storeId = null
    ): array {
        foreach ($optionsData as $shippingOption) {
            $shippingOption->setLabel(
                __($shippingOption->getLabel())->render()
            );

            foreach ($shippingOption->getInputs() as $input) {
                $input->setLabel(
                    __($input->getLabel())->render()
                );
                $input->setTooltip(
                    __($input->getTooltip())->render()
                );
                $input->setPlaceholder(
                    __($input->getPlaceholder())->render()
                );

                if ($comment = $input->getComment()) {
                    $comment->setContent(
                        __($comment->getContent())->render()
                    );
                }

                foreach ($input->getOptions() as $option) {
                    $option->setLabel(
                        __($option->getLabel())->render()
                    );
                }
            }
        }

        return $optionsData;
    }
}
