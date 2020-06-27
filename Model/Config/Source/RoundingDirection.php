<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RoundingDirection implements OptionSourceInterface
{
    const UP = 'up';
    const DOWN = 'down';

    /**
     * Return array of options as value-label pairs
     *
     * @return string[][]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => self::UP,  'label' => __('Round up')],
            ['value' => self::DOWN, 'label' => __('Round down')],
        ];
    }
}
