<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\CodSupportInterface;
use Dhl\ShippingCore\Api\ConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;

/**
 * Class DisableCodPaymentMethods
 *
 * @package Dhl\ShippingCore\Model\Observer
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class DisableCodPaymentMethods implements ObserverInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CodSupportInterface[]
     */
    private $codSupportMap;

    /**
     * DisableCodPaymentMethods constructor.
     *
     * @param ConfigInterface $config
     * @param CodSupportInterface[] $codSupportMap
     */
    public function __construct(ConfigInterface $config, array $codSupportMap = [])
    {
        $this->config = $config;
        $this->codSupportMap = $codSupportMap;
    }

    /**
     * Disable cash on delivery payment methods if carrier does not support them for the given parameters
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var DataObject $checkResult */
        $checkResult = $observer->getEvent()->getDataByKey('result');
        /** @var Quote|null $quote */
        $quote = $observer->getEvent()->getDataByKey('quote');
        if ($quote === null || $checkResult->getData('is_available') === false || $quote->isVirtual()) {
            // not called in checkout or already unavailable
            return;
        }

        /** @var \Magento\Payment\Model\MethodInterface $methodInstance */
        $methodInstance = $observer->getEvent()->getData('method_instance');
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        if (empty($shippingMethod)) {
            return;
        }

        $carrier = strtok($shippingMethod, '_');
        $isCodPaymentMethod = $this->config->isCodPaymentMethod($methodInstance->getCode(), $quote->getStoreId());

        if ($isCodPaymentMethod && isset($this->codSupportMap[$carrier])) {
            $checkResult->setData('is_available', $this->codSupportMap[$carrier]->hasCodSupport($quote));
        }
    }
}
