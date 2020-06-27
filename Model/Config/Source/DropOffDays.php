<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * The available drop off days configuration options.
 */
class DropOffDays implements OptionSourceInterface
{
    /**
     * Returns list of available options.
     *
     * @return string[]
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '1' , 'label' => __('Mon')],
            ['value' => '2' , 'label' => __('Tue')],
            ['value' => '3' , 'label' => __('Wed')],
            ['value' => '4' , 'label' => __('Thu')],
            ['value' => '5' , 'label' => __('Fri')],
            ['value' => '6' , 'label' => __('Sat')],
        ];
    }
}
