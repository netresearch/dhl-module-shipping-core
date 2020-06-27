<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ShippingOptionDisplay implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Cost only')],
            ['value' => '1', 'label' => __('Cost and estimated delivery dates')],
        ];
    }
}
