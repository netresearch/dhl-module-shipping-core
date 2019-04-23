<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Selection;

use Dhl\ShippingCore\Model\ResourceModel\Order\Address\ShippingOptionSelection;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * Class OrderServiceSelectionRepository
 *
 * @package Dhl\ShippingCore\Model
 */
class OrderSelectionRepository
{
    /**
     * @var ShippingOptionSelection
     */
    private $resource;

    /**
     * QuoteServiceSelectionRepository constructor.
     *
     * @param ShippingOptionSelection $resource
     */
    public function __construct(ShippingOptionSelection $resource)
    {
        $this->resource = $resource;
    }

    /**
     * @param OrderSelection $serviceSelection
     * @return OrderSelection
     * @throws CouldNotSaveException
     */
    public function save(OrderSelection $serviceSelection): OrderSelection
    {
        try {
            $this->resource->save($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__('Could not save service selection.'), $exception);
        }

        return $serviceSelection;
    }

    /**
     * @param OrderSelection $serviceSelection
     * @throws CouldNotDeleteException
     */
    public function delete(OrderSelection $serviceSelection)
    {
        try {
            $this->resource->delete($serviceSelection);
        } catch (\Exception $exception) {
            throw new CouldNotDeleteException(__('Could not delete service selection.'), $exception);
        }
    }
}
