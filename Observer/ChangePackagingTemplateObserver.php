<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use \Magento\Framework\Event\Observer;
use \Magento\Framework\Event\ObserverInterface;

class ChangePackagingTemplateObserver implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * @var string[]
     */
    private $supportsCustomPackagingMap;

    /**
     * ChangePackagingTemplateObserver constructor.
     *
     * @param \Magento\Framework\Registry $coreRegistry
     * @param string[] $supportsCustomPackagingMap
     */
    public function __construct(\Magento\Framework\Registry $coreRegistry, array $supportsCustomPackagingMap = [])
    {
        $this->coreRegistry = $coreRegistry;
        $this->supportsCustomPackagingMap = $supportsCustomPackagingMap;
    }

    /**
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        if ($block instanceof \Magento\Shipping\Block\Adminhtml\Order\Packaging
            && $block->getNameInLayout() === 'shipment_packaging'
        ) {
            /** @var \Magento\Sales\Model\Order\Shipment $currentShipment */
            $currentShipment = $this->coreRegistry->registry('current_shipment');
            /** @var \Magento\Sales\Api\Data\OrderInterface|\Magento\Sales\Model\Order $order */
            $order = $currentShipment->getOrder();
            $shippingMethod = $order->getShippingMethod(true);
            if (\in_array($shippingMethod->getData('carrier_code'), $this->supportsCustomPackagingMap, true)) {
                $block->setTemplate('Dhl_ShippingCore::order/packaging/popup.phtml');
            }
        }
    }
}
