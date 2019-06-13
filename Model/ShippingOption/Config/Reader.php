<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Config;

use Magento\Framework\Config\Reader\Filesystem;

/**
 * Class Reader
 *
 * @package Dhl\ShippingCore\Model\ShippingOption\Config
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Reader extends Filesystem
{
    /**
     * @param int|null $scope
     * @return array
     */
    public function read($scope = null): array
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

        $baseCarrier = $result['carriers']['base'];
        unset($result['carriers']['base']);

        foreach ($result['carriers'] as $carrierCode => $carrier) {
            $result['carriers'][$carrierCode] = $this->extendRecursive($baseCarrier, $carrier);
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
}
