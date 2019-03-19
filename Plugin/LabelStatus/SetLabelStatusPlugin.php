<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\LabelStatus;

use Dhl\ShippingCore\Api\LabelStatusManagementInterface;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Api\OrderRepositoryInterface;

/**
 * Class SetLabelStatusPlugin
 *
 * @package Dhl\ShippingCore\Plugin
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class SetLabelStatusPlugin
{
    /**
     * @var LabelStatusManagementInterface
     */
    private $labelStatusManagement;

    /**
     * SetLabelStatusPlugin constructor.
     * @param LabelStatusManagementInterface $labelStatusManagement
     */
    public function __construct(LabelStatusManagementInterface $labelStatusManagement)
    {
        $this->labelStatusManagement = $labelStatusManagement;
    }

    /**
     * Set label status after order save since we need a persisted order for it.
     *
     * @param OrderRepositoryInterface $subject
     * @param OrderInterface $resultOrder
     * @return OrderInterface
     */
    public function afterSave(OrderRepositoryInterface $subject, OrderInterface $resultOrder)
    {
        $this->labelStatusManagement->setInitialStatus($resultOrder);
        return $resultOrder;
    }
}
