<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Order;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Class ItemAttributeReader
 *
 * Read product attributes from shipment items.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ItemAttributeReader
{
    /**
     * Read weight from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return float
     */
    public function getWeight(OrderItemInterface $orderItem): float
    {
        return (float) $orderItem->getWeight();
    }

    /**
     * Read HS code from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getHsCode(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getDhlgwTariffNumber();
    }

    /**
     * Read dangerous goods category from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getDgCategory(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string)$extensionAttributes->getDhlgwDgCategory();
    }

    /**
     * Read export description from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getExportDescription(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getDhlgwExportDescription();
    }

    /**
     * Read country of manufacture from extension attributes.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getCountryOfManufacture(OrderItemInterface $orderItem): string
    {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            return '';
        }

        return (string) $extensionAttributes->getDhlgwCountryOfManufacture();
    }
}
