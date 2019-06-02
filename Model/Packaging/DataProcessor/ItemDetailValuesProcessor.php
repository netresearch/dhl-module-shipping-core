<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ItemDetailValuesProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemDetailValuesProcessor extends AbstractProcessor
{
    /**
     * Set default values for item detail and item customs inputs from the shipment items.
     *
     * @param ItemShippingOptionsInterface[] $itemData
     * @param Shipment $shipment
     *
     * @return ItemShippingOptionsInterface[]
     */
    public function processItemOptions(array $itemData, Shipment $shipment): array
    {
        foreach ($itemData as $itemShippingOptions) {
            $shipmentItem = $this->getMatchingItem($shipment, $itemShippingOptions);
            foreach ($itemShippingOptions->getShippingOptions() as $shippingOption) {
                if ($shippingOption->getCode() === 'details') {
                    $this->setDetailsValues($shippingOption, $shipmentItem);
                } elseif ($shippingOption->getCode() === 'itemCustoms') {
                    $this->setCustomsValues($shippingOption, $shipmentItem);
                }
            }
        }

        return $itemData;
    }

    /**
     * @param ShippingOptionInterface $shippingOption
     * @param Shipment\Item $shipmentItem
     */
    private function setDetailsValues(
        ShippingOptionInterface $shippingOption,
        Shipment\Item $shipmentItem
    ) {
        foreach ($shippingOption->getInputs() as $input) {
            if ($input->getCode() === 'productName') {
                $input->setDefaultValue($shipmentItem->getName());
            } elseif ($input->getCode() === 'weight') {
                $input->setDefaultValue((string) $shipmentItem->getWeight());
            } elseif ($input->getCode() === 'qtyOrdered') {
                $input->setDefaultValue((string) $shipmentItem->getOrderItem()->getQtyOrdered());
            } elseif ($input->getCode() === 'qty') {
                $input->setDefaultValue((string) $shipmentItem->getQty());
            }
        }
    }

    /**
     * @param ShippingOptionInterface $shippingOption
     * @param Shipment\Item $shipmentItem
     */
    private function setCustomsValues(
        ShippingOptionInterface $shippingOption,
        Shipment\Item $shipmentItem
    ) {
        $product = $shipmentItem->getOrderItem()->getProduct();
        if ($product) {
            /** @var ProductInterface $product */
            $tariffNumber = $product->getCustomAttribute(TariffNumber::CODE);
            $dgCategory = $product->getCustomAttribute(DGCategory::CODE);
            $exportDescription = $product->getCustomAttribute(ExportDescription::CODE);

            foreach ($shippingOption->getInputs() as $input) {
                if ($tariffNumber && $input->getCode() === 'hsCode') {
                    $input->setDefaultValue($tariffNumber->getValue() ?? '');
                } elseif ($dgCategory && $input->getCode() === 'dgCategory') {
                    $input->setDefaultValue($dgCategory->getValue() ?? '');
                } elseif ($exportDescription && $input->getCode() === 'exportDescription') {
                    $input->setDefaultValue($exportDescription->getValue() ?? '');
                }
            }
        }
    }

    /**
     * @param Shipment $shipment
     * @param ItemShippingOptionsInterface $itemShippingOptions
     *
     * @return Shipment\Item
     * @throws \RuntimeException
     */
    private function getMatchingItem(
        Shipment $shipment,
        ItemShippingOptionsInterface $itemShippingOptions
    ): Shipment\Item {
        foreach ($shipment->getItems() as $item) {
            if ($item instanceof Shipment\Item
                && (int) $item->getOrderItemId() === $itemShippingOptions->getItemId()
            ) {
                return $item;
            }
        }
        throw new \RuntimeException(
            "Could not find item with order item id {$itemShippingOptions->getItemId()} in shipping options."
        );
    }
}
