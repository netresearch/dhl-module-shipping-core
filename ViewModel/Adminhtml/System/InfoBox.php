<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\ViewModel\Adminhtml\System;

use Dhl\ShippingCore\Model\Config\CoreConfig;
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
     * @var CoreConfig
     */
    private $coreConfig;

    /**
     * InfoBox constructor.
     *
     * @param CoreConfig $coreConfig
     */
    public function __construct(CoreConfig $coreConfig)
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

    public function getModuleTitle(): string
    {
        return 'DHL Shipping Solutions';
    }
}
