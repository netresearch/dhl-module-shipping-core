<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\LabelStatus;

use Dhl\ShippingCore\Api\LabelStatusManagementInterface;
use Dhl\ShippingCore\Model\LabelStatus;
use Dhl\ShippingCore\Model\LabelStatusFactory;
use Dhl\ShippingCore\Model\LabelStatusRepository;
use Dhl\ShippingCore\Model\ResourceModel\LabelStatus\CollectionFactory;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order;
use Psr\Log\LoggerInterface;

/**
 * Class LabelStatusManagement
 *
 * @package Dhl\ShippingCore\Model
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class LabelStatusManagement implements LabelStatusManagementInterface
{
    /**
     * @var string[]
     */
    private $carrierCodes;

    /**
     * @var CollectionFactory
     */
    private $labelStatusCollectionFactory;

    /**
     * @var LabelStatusFactory
     */
    private $labelStatusFactory;

    /**
     * @var LabelStatusRepository
     */
    private $labelStatusRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LabelStatusManagement constructor.
     * @param CollectionFactory $labelStatusCollectionFactory
     * @param LabelStatusFactory $labelStatusFactory
     * @param LabelStatusRepository $labelStatusRepository
     * @param LoggerInterface $logger
     * @param string[] $carrierCodes
     */
    public function __construct(
        CollectionFactory $labelStatusCollectionFactory,
        LabelStatusFactory $labelStatusFactory,
        LabelStatusRepository $labelStatusRepository,
        LoggerInterface $logger,
        array $carrierCodes = []
    ) {
        $this->carrierCodes = $carrierCodes;
        $this->labelStatusCollectionFactory = $labelStatusCollectionFactory;
        $this->labelStatusFactory = $labelStatusFactory;
        $this->labelStatusRepository = $labelStatusRepository;
        $this->logger = $logger;
    }

    /**
     * Set initial label status, order comes in via plugin.
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function setInitialStatus(OrderInterface $order): bool
    {
        $shippingMethod = strtok($order->getShippingMethod(), '_');
        if (!in_array($shippingMethod, $this->carrierCodes, true)) {
            return false;
        }

        $labelStatusCollection = $this->labelStatusCollectionFactory->create();
        $labelStatusCollection->addFieldToFilter('order_id', $order->getId());
        if ($labelStatusCollection->getSize() > 0) {
            return true;
        }

        return $this->setLabelStatusPending($order);
    }

    /**
     * Create label status object with status pending and persist it.
     *
     * @param OrderInterface|Order $order
     * @return bool
     */
    public function setLabelStatusPending(OrderInterface $order): bool
    {
        $labelStatus = $this->labelStatusFactory->create();
        $labelStatus->setData([
            LabelStatus::ORDER_ID => $order->getId(),
            LabelStatus::STATUS_CODE => self::LABEL_STATUS_PENDING
        ]);

        try {
            $this->labelStatusRepository->save($labelStatus);
        } catch (CouldNotSaveException $e) {
            $this->logger->error(__($e->getMessage()));
            return false;
        }

        return true;
    }
}
