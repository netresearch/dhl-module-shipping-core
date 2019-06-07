<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;

/**
 * Class TranslationProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class TranslationProcessor extends AbstractProcessor
{
    /**
     * @param ShippingOptionInterface[] $shippingOptions
     * @param string $countryId Destination country code
     * @param string $postalCode Destination postal code
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $shippingOptions,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($shippingOptions as $shippingOption) {
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

        return $shippingOptions;
    }

    /**
     * @param MetadataInterface $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): MetadataInterface {
        foreach ($metadata->getCommentsBefore() as $comment) {
            $comment->setContent(
                __($comment->getContent())->render()
            );
        }
        foreach ($metadata->getCommentsAfter() as $comment) {
            $comment->setContent(
                __($comment->getContent())->render()
            );
        }

        $metadata->setTitle(
            __($metadata->getTitle())->render()
        );
        foreach ($metadata->getFootnotes() as $footnote) {
            $footnote->setContent(
                __($footnote->getContent())->render()
            );
        }

        return $metadata;
    }

    /**
     * @param CompatibilityInterface[] $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return CompatibilityInterface[]
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($compatibilityData as $compatibility) {
            $compatibility->setErrorMessage(
                __($compatibility->getErrorMessage())->render()
            );
        }

        return $compatibilityData;
    }
}
