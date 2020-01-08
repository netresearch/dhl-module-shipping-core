<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Observer;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

/**
 * JoinOrderItemAttributes Observer
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class JoinOrderItemAttributes implements ObserverInterface
{
    /**
     * @var JoinProcessorInterface
     */
    private $joinProcessor;

    /**
     * JoinOrderItemAttributes constructor.
     *
     * @param JoinProcessorInterface $joinProcessor
     */
    public function __construct(JoinProcessorInterface $joinProcessor)
    {
        $this->joinProcessor = $joinProcessor;
    }

    /**
     * Add joins to order item collection.
     *
     * As opposed to the order collection, the order ITEM collection does not
     * invoke join processors. To fetch additional order item (extension) attributes,
     * the collection gets modified here via join processor. Join directives are
     * defined in extension_attributes.xml file.
     *
     * Event: sales_order_item_collection_load_before
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $orderItemCollection = $observer->getData('order_item_collection');
        if ($orderItemCollection instanceof AbstractDb) {
            $this->joinProcessor->process($orderItemCollection);
        }
    }
}
