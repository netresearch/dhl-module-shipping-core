<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

/**
 * Class DebugLog
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class DebugLog implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => 'ERROR', 'label' =>  'Error'],
            ['value' => 'INFO', 'label' =>  'Info'],
            ['value' => 'DEBUG', 'label' =>  'Debug']
        ];
    }
}
