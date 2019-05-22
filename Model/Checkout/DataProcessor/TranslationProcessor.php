<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

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
     * @param array optionsData
     * @param string $countryId     Destination country code
     * @param string $postalCode    Destination postal code
     * @param int|null $scopeId
     * @return array
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($optionsData as $optionIndex => $shippingOption) {
            $this->translate($optionsData, [$optionIndex, 'label']);
            if (isset($shippingOption['inputs'])) {
                foreach ($shippingOption['inputs'] as $inputIndex => $input) {
                    $this->translate($input, ['label']);
                    $this->translate($input, ['tooltip']);
                    $this->translate($input, ['placeholder']);
                    $this->translate($input, ['comment', 'content']);
                    if (isset($input['options'])) {
                        foreach ($input['options'] as $inputOptionIndex => $inputOption) {
                            $this->translate($input, ['options', $inputOptionIndex, 'label']);
                        }
                    }
                    $optionsData[$optionIndex]['inputs'][$inputIndex] = $input;
                }
            }
        }

        return $optionsData;
    }

    /**
     * @param array $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return array
     */
    public function processMetadata(
        array $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        if (isset($metadata['commentsBefore'])) {
            foreach (array_keys($metadata['commentsBefore']) as $commentIndex) {
                $this->translate($metadata, ['commentsBefore', $commentIndex, 'content']);
            }
        }
        if (isset($metadata['commentsAfter'])) {
            foreach (array_keys($metadata['commentsAfter']) as $commentIndex) {
                $this->translate($metadata, ['commentsAfter', $commentIndex, 'content']);
            }
        }
        $this->translate($metadata, ['title']);
        if (isset($metadata['footnotes'])) {
            foreach ($metadata['footnotes'] as $footnoteIndex => $footnote) {
                $this->translate($metadata, ['footnotes', $footnoteIndex, 'content']);
            }
        }

        return $metadata;
    }

    /**
     * @param array $compatibilityData
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     * @return array
     */
    public function processCompatibilityData(
        array $compatibilityData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        foreach ($compatibilityData as $ruleIndex => $rule) {
            $this->translate($compatibilityData, [$ruleIndex, 'errorMessage']);
        }

        return $compatibilityData;
    }

    /**
     * Translates the string at a given nested array index of the source array.
     *
     * @param array $sourceArray    Passed by reference
     * @param array $arrayLevels
     * @void
     */
    private function translate(array &$sourceArray, array $arrayLevels)
    {
        $reference = &$sourceArray;
        foreach ($arrayLevels as &$key) {
            if (isset($reference[$key])) {
                $reference = &$reference[$key];
            } else {
                return;
            }
        }
        $reference = (string)__($reference);
    }
}
