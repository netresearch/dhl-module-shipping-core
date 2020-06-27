<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\LabelStatus;

use Dhl\ShippingCore\Model\ResourceModel\LabelStatus\CollectionFactory;
use Psr\Log\LoggerInterface;

class LabelStatusProvider
{
    /**
     * @var CollectionFactory
     */
    private $collectionFactory;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * LabelStatusProvider constructor.
     * @param CollectionFactory $collectionFactory
     * @param LoggerInterface $logger
     */
    public function __construct(
        CollectionFactory $collectionFactory,
        LoggerInterface $logger
    ) {
        $this->collectionFactory = $collectionFactory;
        $this->logger = $logger;
    }

    /**
     * Obtain label status for given orders.
     *
     * @param string[] $orderIds
     * @return string[] Array of order id to label status associations: ['order_id' => 'label_status']
     */
    public function getLabelStatus(array $orderIds): array
    {
        try {
            $labelStatusCollection = $this->collectionFactory->create();
            $labelStatusCollection->addFieldToFilter('order_id', ['in' => $orderIds]);
            return $labelStatusCollection->getValues();
        } catch (\Zend_Db_Exception $exception) {
            $this->logger->error('Could not load label status for bulk processing.', ['exception' => $exception]);
            return [];
        }
    }
}
