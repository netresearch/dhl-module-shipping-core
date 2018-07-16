<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class DomesticDefaultProduct
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class DomesticDefaultProduct implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'DOK', 'label' =>  'DOMESTIC EXPRESS 9:00'],
            ['value' => 'DOL', 'label' =>  'DOMESTIC EXPRESS 10:00'],
            ['value' => 'DOT', 'label' =>  'DOMESTIC EXPRESS 12:00'],
            ['value' => 'DOM', 'label' =>  'DOMESTIC EXPRESS']
        ];
    }
}
