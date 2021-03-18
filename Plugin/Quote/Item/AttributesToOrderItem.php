<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Quote\Item;

use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Type\AbstractType;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Quote\Model\Quote\Item;
use Magento\Quote\Model\Quote\Item\AbstractItem;
use Magento\Quote\Model\Quote\Item\ToOrderItem;
use Magento\Sales\Api\Data\OrderItemExtensionInterfaceFactory;
use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Add custom product attributes to order item.
 */
class AttributesToOrderItem
{
    /**
     * @var OrderItemExtensionInterfaceFactory
     */
    private $orderItemExtensionFactory;

    /**
     * AttributesToOrderItem constructor.
     * @param OrderItemExtensionInterfaceFactory $orderItemExtensionFactory
     */
    public function __construct(OrderItemExtensionInterfaceFactory $orderItemExtensionFactory)
    {
        $this->orderItemExtensionFactory = $orderItemExtensionFactory;
    }

    /**
     * Read attribute from quote item.
     *
     * When the quote item is a child item of a bundle or configurable,
     * then it will not have the attribute set. A lookup at the product
     * must be performed instead.
     *
     * @param AbstractItem $quoteItem
     * @param string $attributeCode
     * @return string
     */
    private function readAttribute(AbstractItem $quoteItem, string $attributeCode): string
    {
        // first check child item for the catalog attribute, may be more specific (overwrites the parent)
        if (($quoteItem->getProductType() === Configurable::TYPE_CODE) && (!empty($quoteItem->getChildren()))) {
            $children = $quoteItem->getChildren();
            $childItem = current($children);

            $value = $childItem->getProduct() ? (string)$childItem->getProduct()->getData($attributeCode) : '';
            if ($value) {
                return $value;
            }
        }

        // then check the quote item itself
        if ($quoteItem->hasData($attributeCode)) {
            return (string)$quoteItem->getData($attributeCode);
        }

        // at last, check the product of the quote item if the attribute is available there.
        return $quoteItem->getProduct() ? (string)$quoteItem->getProduct()->getData($attributeCode) : '';
    }

    /**
     * Add additional attributes to order item.
     *
     * When the quote item gets converted to an order item, then additional
     * product attributes are added to the order item. This allows to create
     * a snapshot of the product attributes and prevents accessing products
     * (which might have been deleted) during after-sales processing.
     *
     * @param ToOrderItem $subject
     * @param OrderItemInterface $orderItem
     * @param AbstractItem|Item $item
     * @return OrderItemInterface
     */
    public function afterConvert(
        ToOrderItem $subject,
        OrderItemInterface $orderItem,
        AbstractItem $item
    ): OrderItemInterface {
        if ($orderItem->getIsVirtual()) {
            // virtual items are not shipped, ignore.
            return $orderItem;
        }

        if ($item->getParentItem() && $item->getParentItem()->getProductType() === Configurable::TYPE_CODE) {
            // children of a configurable are not shipped, ignore.
            return $orderItem;
        }

        if ($item->getParentItem() && $item->getParentItem()->getProductType() === Type::TYPE_CODE) {
            $parentOrderItem = $orderItem->getParentItem();
            $shipmentType = (int)$parentOrderItem->getProductOptionByCode('shipment_type');
            if ($shipmentType === AbstractType::SHIPMENT_TOGETHER) {
                // children of a bundle (shipped together) are not shipped, ignore.
                return $orderItem;
            }
        }

        if ($item->getProductType() === Type::TYPE_CODE) {
            $shipmentType = (int)$orderItem->getProductOptionByCode('shipment_type');
            if ($shipmentType === AbstractType::SHIPMENT_SEPARATELY) {
                // a bundle with children (shipped separately) is not shipped, ignore.
                return $orderItem;
            }
        }

        $countryOfManufacture = $this->readAttribute($item, 'country_of_manufacture');
        $dgCategory = $this->readAttribute($item, Constants::ATTRIBUTE_CODE_DG_CATEGORY);
        $exportDescription = $this->readAttribute($item, Constants::ATTRIBUTE_CODE_EXPORT_DESCRIPTION);
        $hsCode = $this->readAttribute($item, Constants::ATTRIBUTE_CODE_TARIFF_NUMBER);
        if (!$countryOfManufacture && !$dgCategory && !$exportDescription && !$hsCode) {
            return $orderItem;
        }

        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            $extensionAttributes = $this->orderItemExtensionFactory->create();
        }

        /** @var Product $product */
        $extensionAttributes->setDhlgwCountryOfManufacture($countryOfManufacture);
        $extensionAttributes->setDhlgwDgCategory($dgCategory);
        $extensionAttributes->setDhlgwExportDescription($exportDescription);
        $extensionAttributes->setDhlgwTariffNumber($hsCode);
        $orderItem->setExtensionAttributes($extensionAttributes);

        return $orderItem;
    }
}
