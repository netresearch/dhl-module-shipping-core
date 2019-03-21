<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Rounded prices type
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @link http://www.netresearch.de/
 */
class RoundedPricesFormat implements ArrayInterface
{
    /**
     * No rounding key.
     */
    const DO_NOT_ROUND = 'no_rounding';

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

            ['value' => self::DO_NOT_ROUND,   'label' => __('Don\'t round prices')],
            ['value' => self::FULL_PRICE,     'label' => __('Round to a whole number (ex. 1 or 37)')],
            ['value' => self::STATIC_DECIMAL, 'label' => __('Round to a specific decimal value (ex. 99 cents)')],
        ];
    }
}
