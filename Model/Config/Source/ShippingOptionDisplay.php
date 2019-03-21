<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShippingOptionDisplay
 *
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @link      http://www.netresearch.de/
 */
class ShippingOptionDisplay implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Cost only')],
            ['value' => '1', 'label' => __('Cost and estimated delivery dates')],
        ];
    }
}
