<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Emulation;

use Magento\Quote\Model\Quote\Address\RateRequest;
use Magento\Shipping\Model\Carrier\AbstractCarrierInterface;

/**
 * Class RateRequestService
 *
 * @package Dhl\ShippingCore\Model\Emulation
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
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
     * @inheritdoc
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
