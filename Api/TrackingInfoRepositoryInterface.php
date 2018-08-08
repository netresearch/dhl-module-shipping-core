<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\TrackingInfoInterface;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;

/**
 * Class TrackingInfoRepositoryInterface.
 *
 * @package Dhl\ShippingCore\Api
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
interface TrackingInfoRepositoryInterface
{
    /**
     * Retrieve tracking information.
     *
     * @param string $trackingNumber The tracking number
     *
     * @return TrackingInfoInterface
     * @throws NoSuchEntityException
     */
    public function getByTrackingNumber(string $trackingNumber): TrackingInfoInterface;

    /**
     * Save tracking information.
     *
     * @param TrackingInfoInterface $trackingInfo The tracking information
     *
     * @return TrackingInfoInterface
     * @throws CouldNotSaveException
     */
    public function save(TrackingInfoInterface $trackingInfo): TrackingInfoInterface;

    /**
     * Delete tracking information.
     *
     * @param TrackingInfoInterface $trackingInfo The tracking information
     *
     * @return bool
     * @throws CouldNotDeleteException
     */
    public function delete(TrackingInfoInterface $trackingInfo): bool;

    /**
     * Delete tracking information using info id.
     *
     * @param string $trackingNumber The tracking number
     *
     * @return bool
     */
    public function deleteByTrackingNumber(string $trackingNumber): bool;
}
