<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Util;

use Magento\Sales\Api\Data\OrderItemInterface;

/**
 * Interface ItemAttributeReaderInterface
 *
 * @api
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://netresearch.de
 */
interface ItemAttributeReaderInterface
{
    /**
     * Read HS code from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getHsCode(OrderItemInterface $orderItem): string;

    /**
     * Read dangerous goods category from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getDgCategory(OrderItemInterface $orderItem): string;

    /**
     * Read export description from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getExportDescription(OrderItemInterface $orderItem): string;

    /**
     * Read country of manufacture from order item.
     *
     * @param OrderItemInterface $orderItem
     * @return string
     */
    public function getCountryOfManufacture(OrderItemInterface $orderItem): string;
}
