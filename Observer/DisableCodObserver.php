<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\CodSupportInterface;
use Dhl\ShippingCore\Model\Config\CoreConfigInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Event\Observer;
use Magento\Quote\Api\Data\CartInterface;
use Magento\Quote\Model\Quote;

/**
 * Class DisableCodObserver
 *
 * @package Dhl\ShippingCore\Model\Observer
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class DisableCodObserver
{
    /** @var CodSupportInterface[] */
    private $codSupportMap;

    /**
     * @var CoreConfigInterface
     */
    private $config;

    /**
     * DisableCodObserver constructor.
     *
     * @param CoreConfigInterface $config
     * @param CodSupportInterface[] $codSupportMap
     */
    public function __construct(CoreConfigInterface $config, array $codSupportMap = [])
    {
        $this->codSupportMap = $codSupportMap;
        $this->config = $config;
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
