<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Logger\Monolog;

/**
 * Class DebugLog
 *
 * @package Dhl\ShippingCore\Model\Backend\Config\Source
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class LogLevel implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray()
    {
        return [
            ['value' => Monolog::ERROR, 'label' => 'Error'],
            ['value' => Monolog::INFO, 'label' => 'Info'],
            ['value' => Monolog::DEBUG, 'label' => 'Debug'],
        ];
    }
}
