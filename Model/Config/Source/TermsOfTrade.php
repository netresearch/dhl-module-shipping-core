<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Option\ArrayInterface;

/**
 * Class TermsOfTrade
 *
 * @author    Rico Sonntag <rico.sonntag@netresearch.de>
 * @link      http://www.netresearch.de/
 */
class TermsOfTrade implements ArrayInterface
{
    const TOD_DDP = 'DDP';
    const TOD_DDU = 'DDU';

    /**
     * Options getter
     *
     * @return mixed[]
     */
    public function toOptionArray(): array
    {
        $optionArray = [];

        $options = $this->toArray();
        foreach ($options as $value => $label) {
            $optionArray[] = ['value' => $value, 'label' => $label];
        }

        return $optionArray;
    }

    /**
     * Get options
     *
     * @return mixed[]
     */
    public function toArray(): array
    {
        return [
            self::TOD_DDU => __('Customer pays duties and taxes (DDU)'),
            self::TOD_DDP => __('I will pay duties and taxes (DTP)'),
        ];
    }
}
