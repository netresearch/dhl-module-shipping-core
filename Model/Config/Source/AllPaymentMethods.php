<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Dhl\ShippingCore\Model\Payment\PaymentData;

/**
 * Payment methods source model.
 *
 * A core bug prevents payment methods from being loaded in M2.3.1.
 * As a workaround, we manipulate the data source before the option values are processed for display.
 *
 * As soon as the issue is resolved, revert back to original class:
 *
 * @see \Magento\Payment\Model\Config\Source\Allmethods
 *
 * @link https://github.com/magento/magento2/issues/22043
 * @link https://bugs.nr/DHLGW-270
 *
 * A previous bug exists in M2.2.4 and M2.2.5 but we decided to drop support for these versions:
 *
 * @link https://github.com/magento/magento2/issues/13460
 * @link https://bugs.nr/DHLVM2-197
 */
class AllPaymentMethods implements OptionSourceInterface
{
    /**
     * Payment data
     *
     * @var PaymentData
     */
    private $paymentData;

    /**
     * @param PaymentData $paymentData
     */
    public function __construct(PaymentData $paymentData)
    {
        $this->paymentData = $paymentData;
    }

    /**
     * @inheritDoc
     */
    public function toOptionArray()
    {
        return $this->paymentData->getPaymentMethodList(true, true, true);
    }
}
