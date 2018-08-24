<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

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
    public function toOptionArray(): array
    {
        return [
            ['value' => (string)Monolog::ERROR, 'label' => 'Error'],
            ['value' => (string)Monolog::INFO, 'label' => 'Info'],
            ['value' => (string)Monolog::DEBUG, 'label' => 'Debug'],
        ];
    }
}
