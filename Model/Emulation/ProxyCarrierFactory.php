<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Emulation;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Class ProxyCarrierFactory
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class ProxyCarrierFactory
{
    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * ProxyCarrierFactory constructor.
     *
     * @param ObjectManagerInterface $objectManager
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        ObjectManagerInterface $objectManager,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->objectManager = $objectManager;
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @param string $carrierCode Carrier model for which the config should be mocked
     * @return AbstractCarrierInterface
     */
    public function create(string $carrierCode): AbstractCarrierInterface
    {
        $carrierClass = $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/model',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        /** @var ScopeConfigInterface $proxyConfig */
        $proxyConfig = $this->objectManager->create(
            ProxyScopeConfig::class,
            [
                'overrideMap' => [
                    'carriers/' . $carrierCode . '/active' => 1,
                ],
            ]
        );

        return $this->objectManager->create($carrierClass, ['scopeConfig' => $proxyConfig]);
    }
}
