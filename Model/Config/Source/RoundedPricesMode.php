<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class Rounded prices
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class RoundedPricesMode implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * No rounding key.
     */
    const DO_NOT_ROUND = 'no_rounding';

    /**
     * Round up key.
     */
    const ROUND_UP = 'round_up';

    /**
     * Round off key.
     */
    const ROUND_OFF = 'round_off';

    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::DO_NOT_ROUND, 'label' => 'Don\'t round'],
            ['value' => self::ROUND_UP, 'label' => 'Round up'],
            ['value' => self::ROUND_OFF, 'label' => 'Round off'],
        ];
    }
}
