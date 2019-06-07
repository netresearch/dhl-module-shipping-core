<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
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
     * @param ShippingOptionInterface[] $optionsData
     * @param string $countryId     Destination country code
     * @param string $postalCode    Destination postal code
     * @param int|null $scopeId
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(
        array $optionsData,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): array {
        uasort($optionsData, [$this, 'sortItems']);
        foreach ($optionsData as $option) {
            $inputArray = $option->getInputs();
            uasort($inputArray, [$this, 'sortItems']);
            $option->setInputs($inputArray);
        }

        return $optionsData;
    }

    /**
     * @param InputInterface|ShippingOptionInterface $a
     * @param InputInterface|ShippingOptionInterface $b
     *
     * @return int
     */
    private function sortItems($a, $b): int
    {
        if ($a->getSortOrder() === $b->getSortOrder()) {
            return 0;
        }

        return ($a->getSortOrder() < $b->getSortOrder()) ? -1 : 1;
    }
}
