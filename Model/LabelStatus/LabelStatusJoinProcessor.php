<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\LabelStatus;

use Dhl\ShippingCore\Api\LabelStatus\LabelStatusManagementInterface;
use Dhl\ShippingCore\Model\ResourceModel\LabelStatus as LabelStatusResource;
use Dhl\ShippingCore\Setup\Module\Constants;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Sales\Api\Data\OrderInterface;

class LabelStatusJoinProcessor implements CollectionProcessorInterface
{
    /**
     * @var LabelStatusResource
     */
    private $labelStatusResource;

    /**
     * LabelStatusJoinProcessor constructor.
     *
     * @param LabelStatusResource $labelStatusResource
     */
    public function __construct(LabelStatusResource $labelStatusResource)
    {
        $this->labelStatusResource = $labelStatusResource;
    }

    /**
     * Add capability to filter orders by label status.
     *
     * @param SearchCriteriaInterface $searchCriteria
     * @param AbstractDb $collection
     */
    public function process(SearchCriteriaInterface $searchCriteria, AbstractDb $collection)
    {
        $tableName = $this->labelStatusResource->getTable(Constants::TABLE_LABEL_STATUS);

        // Do not select any columns as its not required and may lead to invalid results
        // if all (*) columns will selected (e.g. entity_id)
        $collection->getSelect()->joinLeft(
            [
                'status_table' => $tableName
            ],
            sprintf(
                'main_table.%s = status_table.%s',
                OrderInterface::ENTITY_ID,
                LabelStatus::ORDER_ID
            ),
            []
        );

        $collection->addFieldToFilter(
            'status_table.' . LabelStatus::STATUS_CODE,
            [
                ['neq' => LabelStatusManagementInterface::LABEL_STATUS_PROCESSED],
                ['null' => true],
            ]
        );
    }
}
