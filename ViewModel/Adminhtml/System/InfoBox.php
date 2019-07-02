<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\ViewModel\Adminhtml\System;

use Dhl\ShippingCore\Model\Config\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

/**
 * Class InfoBox
 *
 * @package   Dhl\Express\ViewModel
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @link      http://www.netresearch.de/
 */
class InfoBox implements ArgumentInterface
{
    /**
     * @var Config
     */
    private $coreConfig;

    /**
     * InfoBox constructor.
     *
     * @param Config $coreConfig
     */
    public function __construct(Config $coreConfig)
    {
        $this->coreConfig = $coreConfig;
    }

    /**
     * @return string
     */
    public function getModuleVersion(): string
    {
        return $this->coreConfig->getModuleVersion();
    }

    /**
     * @return string
     */
    public function getModuleTitle(): string
    {
        return 'DHL Shipping Solutions';
    }
}
