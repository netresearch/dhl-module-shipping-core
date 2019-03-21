<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Attribute\Source;

use Magento\Eav\Model\Entity\Attribute\Source\AbstractSource;

/**
 * Class DGCategory
 * @package Dhl\ShippingCore\Model\Attribute\Source
 */
class DGCategory extends AbstractSource
{
    const CODE = 'dhl_dangerous_goods_category';

    /**
     * @return string[][]
     */
    public function getAllOptions()
    {
        if (!$this->_options) {
            $this->_options = [
                [
                    'label' => 'none',
                    'value' => ''
                ],
                [
                    'label' => '01 - Lithium Metal Contained in Equipment',
                    'value' => '01'
                ],
                [
                    'label' => '04 - Lithium-Ion Contained in Equipment',
                    'value' => '04'
                ]
            ];
        }

        return $this->_options;
    }
}
