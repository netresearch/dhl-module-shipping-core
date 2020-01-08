<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\ServiceOptions;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\ShippingOptionsProcessorInterface;

/**
 * Class SortOrderProcessor
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class SortOrderProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * Sort shipping options and inputs according to their sort orders.
     *
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
        uasort($optionsData, static function (ShippingOptionInterface $a, ShippingOptionInterface $b) {
            return $a->getSortOrder() - $b->getSortOrder();
        });

        foreach ($optionsData as $option) {
            $inputArray = $option->getInputs();

            uasort($inputArray, static function (InputInterface $a, InputInterface $b) {
                return $a->getSortOrder() - $b->getSortOrder();
            });

            $option->setInputs($inputArray);
        }

        return $optionsData;
    }
}
