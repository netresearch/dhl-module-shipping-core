<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class InternationalDefaultProduct
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class InternationalDefaultProduct implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'XPD', 'label' =>  'EXPRESS ENVELOPE'],
            ['value' => 'DOX', 'label' =>  'EXPRESS WORLDWIDE'],
            ['value' => 'TDK', 'label' =>  'EXPRESS 9:00'],
            ['value' => 'TDL', 'label' =>  'EXPRESS 10:30'],
            ['value' => 'TDT', 'label' =>  'EXPRESS 12:00']
        ];
    }
}
