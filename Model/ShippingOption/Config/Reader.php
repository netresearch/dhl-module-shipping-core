<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Dhl\ShippingCore\Model\Checkout\CheckoutDataProvider;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;

/**
 * Class Reader
 *
 * @package Dhl\ShippingCore\Model\ShippingOption\Config
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Reader extends \Magento\Framework\Config\Reader\Filesystem
{
    public function read($scope = null)
    {
        $result = parent::read($scope);

        return $this->applyBaseConfiguration($result);
    }

    /**
     * Merge data from "base" carrier into other carriers and
     * remove "base" carrier from the data.
     *
     * @param array $result
     * @return array
     */
    private function applyBaseConfiguration(array $result): array
    {
        if (!isset($result['carriers']['base'])) {
            return $result;
        }

        list($baseCarrier, $baseShippingOption, $baseInput, $baseOption, $result) = $this->extractBaseElements($result);

        $groups = [
            PackagingDataProvider::GROUP_ITEM_LEVEL,
            PackagingDataProvider::GROUP_PACKAGE_LEVEL,
        ];
        foreach ($result['carriers'] as $carrierCode => $carrier) {
            $result['carriers'][$carrierCode] = $this->extendRecursive($baseCarrier, $carrier);
            foreach ($groups as $group) {
                foreach ($carrier[$group] ?? [] as $optionCode => $shippingOption) {
                    $result['carriers'][$carrierCode][$group][$optionCode]
                        = $this->extendRecursive($baseShippingOption, $shippingOption);
                    foreach ($shippingOption['inputs'] ?? [] as $inputCode => $input) {
                        $result['carriers'][$carrierCode][$group][$optionCode]['inputs'][$inputCode]
                            = $this->extendRecursive($baseInput, $input);
                        foreach ($input['options'] ?? [] as $optionId => $option) {
                            $result['carriers'][$carrierCode][$group][$optionCode]['inputs'][$inputCode]['options'][$optionId]
                                = $this->extendRecursive($baseOption, $option);
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Recursively extend a nested base array with another array's values.
     * The extension array will override any values already defined in the base array.
     *
     * This is different from array_merge_recursive in that non-array values are actually overwritten by the
     * extension array.
     *
     * @param $baseArray
     * @param $extensionArray
     * @return array
     */
    private function extendRecursive($baseArray, $extensionArray): array
    {
        foreach ($extensionArray as $key => $value) {
            if (!is_array($value) || !isset($baseArray[$key])) {
                $baseArray[$key] = $value;
            } elseif (is_array($value)) {
                $baseArray[$key] = $this->extendRecursive($baseArray[$key], $value);
            }
        }

        return $baseArray;
    }

    /**
     * @param array $result
     * @return array
     */
    private function extractBaseElements(array $result): array
    {
        $baseCarrier = $result['carriers']['base'];
        $baseCarrier['baseLevelOptions'] = $this->extendRecursive(
            $baseCarrier[PackagingDataProvider::GROUP_ITEM_LEVEL],
            $baseCarrier[PackagingDataProvider::GROUP_PACKAGE_LEVEL]
        );

        $baseShippingOption = $baseCarrier['baseLevelOptions']['base'];
        $baseInput = $baseShippingOption['inputs']['base'];
        $baseOption = $baseInput['options']['base'];
        $baseOption['id'] = '';

        unset(
            $baseShippingOption['inputs'],
            $baseCarrier['baseLevelOptions'],
            $baseCarrier['packageLevelOptions']['base'],
            $baseCarrier['itemLevelOptions']['base'],
            $baseInput['options']['base'],
            $result['carriers']['base']
        );

        return [$baseCarrier, $baseShippingOption, $baseInput, $baseOption, $result];
    }
}
