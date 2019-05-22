<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;

/**
 * Class SortOrderProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class SortOrderProcessor extends AbstractProcessor
{
    /**
     * Sort shipping options and inputs according to their sort orders.
     *
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
        usort($optionsData, [$this, 'sortItems']);

        foreach (array_keys($optionsData) as $optionsIndex) {
            usort($optionsData[$optionsIndex]['inputs'], [$this, 'sortItems']);
        }

        return $optionsData;
    }

    /**
     * @param array $a
     * @param array $b
     * @return int
     */
    private function sortItems(array $a, array $b): int
    {
        $sortOrder1 = $a['sortOrder'] ?? false;
        $sortOrder2 = $b['sortOrder'] ?? false;
        if ($sortOrder1 === false || $sortOrder2 === false || $sortOrder1 === $sortOrder2) {
            return 0;
        }

        return ($sortOrder1 < $sortOrder2) ? -1 : 1;
    }
}
