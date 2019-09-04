<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor\ServiceOptions;

use Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\ShippingOptionsProcessorInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class SortOrderProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 */
class SortOrderProcessor implements ShippingOptionsProcessorInterface
{
    /**
     * Sort shipping options and inputs according to their sort orders.
     *
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, Shipment $shipment): array
    {
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
