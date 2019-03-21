<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\CodSupportInterface;
use Dhl\ShippingCore\Model\Config\CoreConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class DisableCodObserver
 *
 * @package Dhl\ShippingCore\Model\Observer
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class DisableCodObserver implements ObserverInterface
{
    /**
     * @var CoreConfigInterface
     */
    private $config;

    /**
     * @var CodSupportInterface[]
     */
    private $codSupportMap;

    /**
     * DisableCodObserver constructor.
     *
     * @param CoreConfigInterface $config
     * @param CodSupportInterface[] $codSupportMap
     */
    public function __construct(CoreConfigInterface $config, array $codSupportMap = [])
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
        /** @var CartInterface|Quote $quote */
        $quote = $observer->getEvent()->getDataByKey('quote');
        if ($quote === null || $checkResult->getData('is_available') === false || $quote->isVirtual()) {
            // not called in checkout or already unavailable
            return;
        }

        /** @var \Magento\Payment\Model\MethodInterface $methodInstance */
        $methodInstance = $observer->getEvent()->getData('method_instance');
        $shippingMethod = $quote->getShippingAddress()->getShippingMethod();
        $methodParts = explode('_', $shippingMethod);
        $carrier = array_shift($methodParts);
        if (\in_array($carrier, $this->codSupportMap, true)
            && $this->config->isCodPaymentMethod(
                $methodInstance->getCode(),
                $quote->getStoreId()
            )
        ) {
            $checkResult->setData('is_available', $this->codSupportMap[$carrier]->hasCodSupport($quote));
        }
    }
}
