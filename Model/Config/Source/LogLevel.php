<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Logger\Monolog;
use Magento\Framework\Option\ArrayInterface;

/**
 * Class LogLevel
 *
 * @author    Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
 */
class LogLevel implements ArrayInterface
{
    /**
     * {@inheritdoc}
     */
    public function toOptionArray(): array
    {
        return [
            ['value' => (string) Monolog::ERROR, 'label' => __('Errors')],
            ['value' => (string) Monolog::INFO,  'label' => __('Info (Errors and Warnings)')],
            ['value' => (string) Monolog::DEBUG, 'label' => __('Debug (All API Activities)')],
        ];
    }
}
