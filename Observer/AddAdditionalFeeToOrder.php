<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\Quote;
use Magento\Sales\Model\Order;

/**
 * AfterOrder Observer.
 *
 * @package  Dhl\ShippingCore\Observer
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class AddAdditionalFeeToOrder implements ObserverInterface
{
    /**
     * @var TotalsManager
     */
    private $totalsManager;

    /**
     * AddAdditionalFeeToOrder constructor.
     *
     * @param TotalsManager $totalsManager
     */
    public function __construct(TotalsManager $totalsManager)
    {
        $this->totalsManager = $totalsManager;
    }

    /**
     * Transfer additional fees from Quote to Order on order creation
     *
     * @event sales_model_service_quote_submit_before
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var Quote $quote */
        $quote = $observer->getData('quote');
        /** @var Order $order */
        $order = $observer->getData('order');

        $this->totalsManager->transferAdditionalFees($quote, $order);
    }
}
