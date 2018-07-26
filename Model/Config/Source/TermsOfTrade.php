<?php

/**
 * See LICENSE.md for license details.
 */
namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class TermsOfTrade
 *
 * @package Dhl\Shipping\Model
 */
class TermsOfTrade
{

    const TOD_DDP = 'DDP';
    const TOD_DDU = 'DDU';

    /**
     * Options getter
     *
     * @return mixed[]
     */
    public function toOptionArray()
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
    public function toArray()
    {
        return [
            self::TOD_DDP => 'DDP',
            self::TOD_DDU => 'DDU'
        ];
    }
}
