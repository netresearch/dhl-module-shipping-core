<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor\ServiceOptions;

use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\ShippingOptionsProcessorInterface;

/**
 * Class TranslationProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
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
