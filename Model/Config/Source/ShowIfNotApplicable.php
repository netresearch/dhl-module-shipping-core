<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class ShowIfNotApplicable implements OptionSourceInterface
{
    /**
     * @inheritDoc
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => '0', 'label' => __('Hide this shipping method in checkout')],
            ['value' => '1', 'label' => __('Display customized message')],
        ];
    }
}
