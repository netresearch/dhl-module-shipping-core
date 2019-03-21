<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class CustomizeApplicableCountries
 *
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @link      http://www.netresearch.de/
 */
class CustomizeApplicableCountries implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Use default countries from General > Country')],
            ['value' => '1', 'label' => __('Create a customized country list')],
        ];
    }
}
