<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class PickupType
 *
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @link      http://www.netresearch.de/
 */
class PickupType implements ArrayInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '1', 'label' => __('Regularly scheduled pickup')],
            ['value' => '0', 'label' => __('Ad hoc pickup or service point drop-off')],
        ];
    }
}
