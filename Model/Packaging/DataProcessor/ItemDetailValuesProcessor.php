<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\Data\ShipmentItemInterface;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class ItemDetailValuesProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemDetailValuesProcessor extends AbstractProcessor
{
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_ITEM) {
            return $optionsData;
        }

        foreach ($shipment->getItems() as $item) {
            $itemId = $item->getOrderItemId();
            $optionsData[$itemId]['details']['inputs'] = $this->addDetailsData(
                $item,
                $optionsData[$itemId]['details']['inputs']
            );
            $optionsData[$itemId]['itemCustoms']['inputs'] = $this->addCustomsData(
                $item,
                $optionsData[$itemId]['itemCustoms']['inputs']
            );
        }

        return $optionsData;
    }

    /**
     * @param ShipmentItemInterface $item
     * @param array $customsInputs
     * @return array
     */
    private function addCustomsData(ShipmentItemInterface $item, array $customsInputs): array
    {
        /** @var OrderItemInterface $orderItem */
        $orderItem = $item->getOrderItem();
        if ($orderItem) {
            /** @var ProductInterface $product */
            $product = $orderItem->getProduct();
            if ($product) {
                $tariffNumber = $product->getCustomAttribute(TariffNumber::CODE);
                $dgCategory = $product->getCustomAttribute(DGCategory::CODE);
                $exportDescription = $product->getCustomAttribute(ExportDescription::CODE);

                if ($tariffNumber) {
                    $customsInputs['hsCode']['defaultValue'] = $tariffNumber->getValue() ?? '';
                }
                if ($dgCategory) {
                    $customsInputs['dgCategory']['defaultValue'] = $dgCategory->getValue() ?? '';
                }
                if ($exportDescription) {
                    $customsInputs['exportDescription']['defaultValue'] = $exportDescription->getValue() ?? '';
                }
            }
        }

        return $customsInputs;
    }

    /**
     * @param \Magento\Sales\Api\Data\ShipmentItemInterface $item
     * @param array $detailsInputs
     * @return array
     */
    private function addDetailsData(\Magento\Sales\Api\Data\ShipmentItemInterface $item, array $detailsInputs): array
    {
        $detailsInputs['productName']['defaultValue'] = $item->getName();
        $detailsInputs['weight']['defaultValue'] = $item->getWeight();

        /** @var OrderItemInterface $orderItem */
        $orderItem = $item->getOrderItem();
        if ($orderItem) {
            $detailsInputs['qtyOrdered']['defaultValue'] = $orderItem->getQtyOrdered();
            $detailsInputs['qty']['defaultValue'] = $item->getQty();
        }

        return $detailsInputs;
    }
}
