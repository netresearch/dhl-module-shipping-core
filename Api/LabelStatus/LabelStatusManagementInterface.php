<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\LabelStatus;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * @api
 */
interface LabelStatusManagementInterface
{
    const LABEL_STATUS_PENDING   = 'pending';
    const LABEL_STATUS_PARTIAL   = 'partial';
    const LABEL_STATUS_PROCESSED = 'processed';
    const LABEL_STATUS_FAILED    = 'failed';

    /**
     * Set the initial label status.
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setInitialStatus(OrderInterface $order): bool;

    /**
     * Set label status "pending".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusPending(OrderInterface $order): bool;

    /**
     * Set label status "processed".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusProcessed(OrderInterface $order): bool;

    /**
     * Set label status "failed".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusFailed(OrderInterface $order): bool;

    /**
     * Set label status "partial".
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusPartial(OrderInterface $order): bool;
}
