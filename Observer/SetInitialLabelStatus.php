<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * Class SetLabelStatusObserver
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class SetInitialLabelStatus implements ObserverInterface
{
    /**
     * @var LabelStatusManagementInterface
     */
    private $labelStatusManagement;

    /**
     * SetLabelStatusObserver constructor.
     * @param LabelStatusManagementInterface $labelStatusManagement
     */
    public function __construct(LabelStatusManagementInterface $labelStatusManagement)
    {
        $this->labelStatusManagement = $labelStatusManagement;
    }

    /**
     * Trigger setting of initial label status.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $order = $observer->getData('order');
        $this->labelStatusManagement->setInitialStatus($order);
    }
}
