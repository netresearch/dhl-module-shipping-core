<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Emulation;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\NotFoundException;
use Magento\Framework\ObjectManagerInterface;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Class ProxyCarrierFactory
 *
 * fixme(nr): make it public (api)
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author  Paul Siedler <paul.siedler@netresearch.de>
 * @link    https://www.netresearch.de/
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
     * Creates a carrier model with a partially overwritten config (enforce carrier to be active)
     *
     * @param string $carrierCode Carrier model for which the config should be mocked
     * @return AbstractCarrierInterface
     * @throws NotFoundException Requested carrier not found
     * @throws \Exception Object manager / factory error
     */
    public function create(string $carrierCode): AbstractCarrierInterface
    {
        $carrierClass = $this->scopeConfig->getValue(
            'carriers/' . $carrierCode . '/model',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );

        if (empty($carrierClass)) {
            throw new NotFoundException(__('Carrier "%1" not found.', $carrierClass));
        }

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
