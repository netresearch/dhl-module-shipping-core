<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\ViewModel\Adminhtml\System;

use Dhl\ShippingCore\Model\Config\Config;
use Magento\Framework\View\Element\Block\ArgumentInterface;

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
}
