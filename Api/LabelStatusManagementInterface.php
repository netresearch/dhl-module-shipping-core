<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Magento\Sales\Api\Data\OrderInterface;

/**
 * Interface LabelStatusManagementInterface
 *
 * @package Dhl\ShippingCore\API
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @copyright 2019 Netresearch GmbH
 * @link https://www.netresearch.de/
 *
 * @api
 */
interface LabelStatusManagementInterface
{
    const LABEL_STATUS_PENDING = 'pending';

    const LABEL_STATUS_PROCESSED = 'processed';

    const LABEL_STATUS_FAILED = 'failed';

    /**
     * Set the initial label status.
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setInitialStatus(OrderInterface $order): bool;

    /**
     * Set label status pending.
     *
     * @param OrderInterface $order
     * @return bool
     */
    public function setLabelStatusPending(OrderInterface $order): bool;
}
