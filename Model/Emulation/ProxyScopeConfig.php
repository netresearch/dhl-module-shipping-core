<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Emulation;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class ProxyScopeConfig
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link http://www.netresearch.de/
 */
class ProxyScopeConfig implements ScopeConfigInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var string[]
     */
    private $overrideMap;

    /**
     * ProxyScopeConfig constructor.
     *
     * @param ScopeConfigInterface $scopeConfig
     * @param array $overrideMap
     */
    public function __construct(ScopeConfigInterface $scopeConfig, $overrideMap = [])
    {
        $this->scopeConfig = $scopeConfig;
        $this->overrideMap = $overrideMap;
    }

    /**
     * @inheritdoc
     */
    public function getValue($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        if (array_key_exists($path, $this->overrideMap)) {
            return $this->overrideMap[$path];
        }

        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @inheritdoc
     */
    public function isSetFlag($path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        if (array_key_exists($path, $this->overrideMap)) {
            return $this->overrideMap[$path];
        }

        return $this->scopeConfig->isSetFlag($path, $scopeType, $scopeCode);
    }
}
