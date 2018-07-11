<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Emulation;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Class RateRequestService
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class RateRequestService implements \Dhl\ShippingCore\Api\RateRequestEmulationInterface
{
    /**
     * @var ProxyCarrierFactory
     */
    private $proxyCarrierFactory;

    /**
     * @var AbstractCarrierInterface[]
     */
    private $emulatedCarriers = [];

    /**
     * RateRequestEmulator constructor.
     *
     * @param ProxyCarrierFactory $proxyCarrierFactory
     */
    public function __construct(ProxyCarrierFactory $proxyCarrierFactory)
    {
        $this->proxyCarrierFactory = $proxyCarrierFactory;
    }

    /**
     * @param string $carrierCode
     * @param RateRequest $request
     * @return bool|\Magento\Framework\DataObject|null
     */
    public function emulateRateRequest(string $carrierCode, RateRequest $request)
    {
        if (!array_key_exists($carrierCode, $this->emulatedCarriers)) {
            /** @var AbstractCarrierInterface $proxyCarrier */
            $proxyCarrier = $this->proxyCarrierFactory->create($carrierCode);
            $this->emulatedCarriers[$carrierCode] = $proxyCarrier;
        }

        return $this->emulatedCarriers[$carrierCode]->collectRates($request);
    }
}
