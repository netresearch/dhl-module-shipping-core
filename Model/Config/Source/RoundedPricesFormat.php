<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class Rounded prices type
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class RoundedPricesFormat implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Full price key.
     */
    const FULL_PRICE = 'full_price';

    /**
     * Static decimal key.
     */
    const STATIC_DECIMAL = 'static_decimal';
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::FULL_PRICE, 'label' => 'Round to integer'],
            ['value' => self::STATIC_DECIMAL, 'label' => 'Round to specific decimal value'],
        ];
    }
}
