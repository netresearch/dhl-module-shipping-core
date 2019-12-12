<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ResourceModel\RecipientStreet;

use Dhl\ShippingCore\Model\SplitAddress\RecipientStreet;
use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

/**
 * Collection
 *
 * @package  Dhl\ShippingCore\Model\ResourceModel
 * @author   Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link     https://www.netresearch.de/
 */
class Collection extends AbstractCollection
{
    /**
     * Initialization
     */
    public function _construct()
    {
        $this->_init(RecipientStreet::class, RecipientStreetResource::class);
    }
}
