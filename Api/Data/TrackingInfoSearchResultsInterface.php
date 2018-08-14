<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

use Magento\Framework\Api\SearchResultsInterface;

/**
 * Class TrackingInfoSearchResultsInterface.
 *
 * @package Dhl\ShippingCore\Api
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
interface TrackingInfoSearchResultsInterface extends SearchResultsInterface
{
    /**
     * Get tracking info item list.
     *
     * @return TrackingInfoInterface[]
     */
    public function getItems();

    /**
     * Set tracking info item list.
     *
     * @param TrackingInfoInterface[] $items
     *
     * @return $this
     */
    public function setItems(array $items);
}
