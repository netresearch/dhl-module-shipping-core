<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ItemAttribute;

use Dhl\ShippingCore\Api\Data\OrderItemAttributesInterface;
use Dhl\ShippingCore\Api\Data\OrderItemAttributesInterfaceFactory;
use Dhl\ShippingCore\Model\ItemAttribute\OrderItemAttributes;
use Dhl\ShippingCore\Model\ResourceModel\OrderItemAttributes as OrderItemAttributeResource;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * OrderItemAttributesRepository
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class OrderItemAttributesRepository
{
    /**
     * @var OrderItemAttributeResource
     */
    private $resource;

    /**
     * @var OrderItemAttributesInterfaceFactory
     */
    private $orderItemAttributeFactory;

    /**
     * OrderAddressRepository constructor.
     *
     * @param OrderItemAttributeResource $resource
     * @param OrderItemAttributesInterfaceFactory $orderItemAttributeFactory
     */
    public function __construct(
        OrderItemAttributeResource $resource,
        OrderItemAttributesInterfaceFactory $orderItemAttributeFactory
    ) {
        $this->resource = $resource;
        $this->orderItemAttributeFactory = $orderItemAttributeFactory;
    }

    /**
     * Persist the order item attribute object.
     *
     * @param OrderItemAttributesInterface $orderItemAttribute
     * @return OrderItemAttributesInterface
     * @throws CouldNotSaveException
     */
    public function save(OrderItemAttributesInterface $orderItemAttribute): OrderItemAttributesInterface
    {
        try {
            /** @var OrderItemAttributes $orderItemAttribute */
            $this->resource->save($orderItemAttribute);
        } catch (\Exception $exception) {
            $msg = __('Unable to save additional attributes for order item %1: %2', $orderItemAttribute->getItemId(), $exception->getMessage());
            throw new CouldNotSaveException($msg);
        }

        return $orderItemAttribute;
    }

    /**
     * Get order item attribute by order item id.
     *
     * @param int $orderItemId
     * @return OrderItemAttributesInterface
     * @throws NoSuchEntityException
     */
    public function get(int $orderItemId): OrderItemAttributesInterface
    {
        /** @var OrderItemAttributes $orderItemAttribute */
        $orderItemAttribute = $this->orderItemAttributeFactory->create();

        try {
            $this->resource->load($orderItemAttribute, $orderItemId);
        } catch (\Exception $exception) {
            throw new NoSuchEntityException(__('Unable to load additional attributes for order item %1.', $orderItemId));
        }

        if (!$orderItemAttribute->getId()) {
            throw new NoSuchEntityException(__('No additional attributes found for order item %1.', $orderItemId));
        }

        return $orderItemAttribute;
    }
}
