<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\ItemAttributeReader;
use Magento\Catalog\Model\Product\Attribute\Source\Countryofmanufacture;
use Magento\Sales\Model\Order\Shipment;
use Magento\Sales\Model\Order\Shipment\Item;

/**
 * Class ItemInputDataProcessor
 *
 * Prefill item level inputs with
 * - a set of possible options (e.g. country list)
 * - predefined catalog data (e.g. item weight, country of manufacture)
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemInputDataProcessor extends AbstractProcessor
{
    /**
     * @var ItemAttributeReader
     */
    private $itemAttributeReader;

    /**
     * @var Countryofmanufacture
     */
    private $countrySource;

    /**
     * @var CommentInterfaceFactory
     */
    private $commentFactory;

    /**
     * ItemInputDataProcessor constructor.
     * @param ItemAttributeReader $itemAttributeReader
     * @param Countryofmanufacture $countrySource
     * @param CommentInterfaceFactory $commentFactory
     */
    public function __construct(
        ItemAttributeReader $itemAttributeReader,
        Countryofmanufacture $countrySource,
        CommentInterfaceFactory $commentFactory
    ) {
        $this->itemAttributeReader = $itemAttributeReader;
        $this->countrySource = $countrySource;
        $this->commentFactory = $commentFactory;
    }

    /**
     * @param int $orderItemId
     * @param Item[] $shipmentItems
     * @return Item
     * @throws \RuntimeException
     */
    private function getShipmentItemByOrderItemId(int $orderItemId, array $shipmentItems): Item
    {
        foreach ($shipmentItems as $shipmentItem) {
            if ((int) $shipmentItem->getOrderItemId() === $orderItemId) {
                return $shipmentItem;
            }
        }

        throw new \RuntimeException("Order item with ID $orderItemId not found.");
    }

    /**
     * Set options and values to inputs on item level.
     *
     * @param ShippingOptionInterface $shippingOption
     * @param Item $shipmentItem
     */
    private function processInputs(ShippingOptionInterface $shippingOption, Item $shipmentItem)
    {
        foreach ($shippingOption->getInputs() as $input) {
            switch ($input->getCode()) {
                // details
                case 'productId':
                    $input->setDefaultValue((string) $shipmentItem->getProductId());
                    break;
                case 'productName':
                    $input->setDefaultValue((string) $shipmentItem->getName());
                    break;
                case 'price':
                    $input->setDefaultValue((string) $shipmentItem->getPrice());
                    break;
                case 'weight':
                    $input->setDefaultValue((string) $this->itemAttributeReader->getWeight($shipmentItem));
                    break;
                case 'qtyToShip':
                    $input->setDefaultValue((string) $shipmentItem->getOrderItem()->getQtyOrdered());
                    break;
                case 'qty':
                    $input->setDefaultValue((string) $shipmentItem->getQty());
                    break;
                // customs
                case 'hsCode':
                    $input->setDefaultValue($this->itemAttributeReader->getHsCode($shipmentItem));
                    break;
                case 'dgCategory':
                    $input->setDefaultValue($this->itemAttributeReader->getDgCategory($shipmentItem));
                    break;
                case 'exportDescription':
                    $input->setDefaultValue($this->itemAttributeReader->getExportDescription($shipmentItem));
                    break;
                case 'customsValue':
                    $price = (float) $shipmentItem->getPrice();
                    $currency = $shipmentItem->getShipment()->getStore()->getBaseCurrency();
                    $currencySymbol = $currency->getCurrencySymbol() ?: $currency->getCode();
                    $comment = $this->commentFactory->create();
                    $comment->setContent($currencySymbol);
                    $input->setComment($comment);
                    $input->setDefaultValue((string) number_format($price, 2));
                    break;
                case 'countryOfOrigin':
                    $input->setOptions($this->countrySource->getAllOptions());
                    $input->setDefaultValue($this->itemAttributeReader->getCountryOfManufacture($shipmentItem));
                    break;
            }
        }
    }

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
            try {
                $shipmentItem = $this->getShipmentItemByOrderItemId(
                    $itemShippingOptions->getItemId(),
                    $shipment->getAllItems()
                );
            } catch (\RuntimeException $exception) {
                continue;
            }

            foreach ($itemShippingOptions->getShippingOptions() as $optionGroup) {
                $this->processInputs($optionGroup, $shipmentItem);
            }
        }

        return $itemData;
    }
}
