<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Payment\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Magento\Payment\Helper\Data;

/**
 * Payment methods source model.
 *
 * A core bug prevents payment methods from being listed in groups.
 * As a workaround, display them as flat list. As soon as the issue is resolved,
 * revert back to original class.
 *
 * @see \Magento\Payment\Model\Config\Source\Allmethods
 * @link https://github.com/magento/magento2/issues/13460
 * @link https://bugs.nr/DHLVM2-197
 *
 * @package  Dhl\Shipping\Model
 * @author   Ronny gertler <ronny.gertler@netresearch.de>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.netresearch.de/
 */
class Allmethods implements OptionSourceInterface
{
    /**
     * @var Data
     */
    private $paymentData;

    /**
     * @param Data $paymentData
     */
    public function __construct(Data $paymentData)
    {
        $this->paymentData = $paymentData;
    }

    /**
     * @return string[][]
     */
    public function toOptionArray()
    {
        return $this->paymentData->getPaymentMethodList(true, true, false);
    }
}
