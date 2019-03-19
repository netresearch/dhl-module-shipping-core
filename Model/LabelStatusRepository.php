<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Dhl\ShippingCore\Model\ResourceModel\LabelStatus as Resource;
use Magento\Framework\Exception\CouldNotSaveException;

/**
 * LabelStatusRepository
 *
 * @package Dhl\ShippingCore\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class LabelStatusRepository
{
    /**
     * @var Resource
     */
    private $resource;

    /**
     * LabelStatusRepository constructor.
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Persist the label status object.
     *
     * @param LabelStatus $labelStatus
     * @return LabelStatus
     * @throws CouldNotSaveException
     */
    public function save(LabelStatus $labelStatus): LabelStatus
    {
        try {
            $this->resource->save($labelStatus);
        } catch (\Exception $exception) {
            throw new CouldNotSaveException(__($exception->getMessage()));
        }

        return $labelStatus;
    }
}
