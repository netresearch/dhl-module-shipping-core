<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;

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
     * @var \Dhl\ShippingCore\Model\ResourceModel\LabelStatus
     */
    private $resource;

    /**
     * LabelStatusRepository constructor.
     *
     * @param \Dhl\ShippingCore\Model\ResourceModel\LabelStatus $resource
     */
    public function __construct(\Dhl\ShippingCore\Model\ResourceModel\LabelStatus $resource)
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
            if ($exception instanceof LocalizedException) {
                throw new CouldNotSaveException(__($exception->getMessage()));
            }

            throw new CouldNotSaveException(__('Unable to save label status'), $exception);
        }

        return $labelStatus;
    }
}
