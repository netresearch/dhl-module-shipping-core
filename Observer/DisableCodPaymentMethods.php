<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\PaymentMethod\MethodAvailabilityInterface;
use Dhl\ShippingCore\Api\ConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

class DisableCodPaymentMethods implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var MethodAvailabilityInterface[]
     */
    private $codSupportMap;

    /**
     * DisableCodPaymentMethods constructor.
     *
     * @param ConfigInterface $config
     * @param MethodAvailabilityInterface[] $codSupportMap
     */
    public function __construct(ConfigInterface $config, array $codSupportMap = [])
    {
        $this->config = $config;
        $this->codSupportMap = $codSupportMap;
    }

    /**
     * Disable cash on delivery payment methods if carrier does not support them for the given parameters.
     *
     * COD will not be disabled for virtual quotes, these will not be processed with DHL.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $checkResult */
        $checkResult = $observer->getData('result');
        /** @var Quote|null $quote */
        $quote = $observer->getData('quote');
        if ($quote === null || $checkResult->getData('is_available') === false || $quote->isVirtual()) {
            // not called in checkout or already unavailable
            return;
        }

        /** @var \Magento\Payment\Model\MethodInterface $methodInstance */
        $methodInstance = $observer->getData('method_instance');
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if (empty($shippingMethod)) {
            return;
        }

        $carrier = strtok($shippingMethod, '_');
        $isCodPaymentMethod = $this->config->isCodPaymentMethod($methodInstance->getCode(), $quote->getStoreId());

        if ($isCodPaymentMethod && isset($this->codSupportMap[$carrier])) {
            $checkResult->setData('is_available', $this->codSupportMap[$carrier]->isAvailable($quote));
        }
    }
}
