<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class PickupType implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            ['value' => '1', 'label' => __('Regularly scheduled pickup')],
            ['value' => '0', 'label' => __('Ad hoc pickup or service point drop-off')],
        ];
    }
}
