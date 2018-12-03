<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class ShowIfNotApplicable
 *
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
 */
class ShowIfNotApplicable implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Hide this option from customer')],
            ['value' => '1', 'label' => __('Display customized message')],
        ];
    }
}
