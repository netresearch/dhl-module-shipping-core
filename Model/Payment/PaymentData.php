<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Payment;

use Magento\Payment\Helper\Data;
use Magento\Payment\Model\MethodInterface;

/**
 * Class PaymentData
 *
 * @package Dhl\ShippingCore\Model
 */
class PaymentData extends Data
{
    /**
     * Set active flag based on current store configuration, not config defaults.
     *
     * The core payment methods source model is broken again. It displays ony methods enabled in config defaults.
     * The broken method is very long so we fix the issue by setting the active flag before the broken method is called.
     *
     * @see \Magento\Payment\Model\Config\Source\Allmethods::toOptionArray
     * @see \Magento\Payment\Helper\Data::getPaymentMethodList
     * @see \Magento\Payment\Helper\Data::getPaymentMethods
     *
     * @return mixed[]
     */
    public function getPaymentMethods()
    {
        // display all methods or only methods activated in store configuration
        $allMethods = true;

        /** @var MethodInterface[] $activeMethods */
        $activeMethods = $this->_paymentConfig->getActiveMethods();

        $methods = parent::getPaymentMethods();

        foreach ($methods as $key => &$methodData) {
            if ($allMethods) {
                $methodData['active'] = true;
            } else {
                $methodData['active'] = isset($activeMethods[$key]) && $activeMethods[$key]->isActive();
            }
        }

        return $methods;
    }
}
