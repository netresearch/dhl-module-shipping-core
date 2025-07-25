<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class RoundingFormat implements OptionSourceInterface
{
    public const INTEGER = 'integer';
    public const DECIMAL = 'decimal';

    /**
     * Return array of options as value-label pairs
     *
     * @return string[][]
     */
    #[\Override]
    public function toOptionArray(): array
    {
        return [
            ['value' => self::INTEGER, 'label' => __('Round to a whole number (ex. 1 or 37)')],
            ['value' => self::DECIMAL, 'label' => __('Round to a specific decimal value (ex. 99 cents)')],
        ];
    }
}
