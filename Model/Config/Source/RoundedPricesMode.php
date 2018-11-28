<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class Rounded prices
 *
 * @author    Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
 */
class RoundedPricesMode implements ArrayInterface
{
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
            ['value' => self::ROUND_UP,  'label' => __('Round up')],
            ['value' => self::ROUND_OFF, 'label' => __('Round down')],
        ];
    }
}
