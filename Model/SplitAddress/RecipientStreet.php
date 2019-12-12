<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\SplitAddress;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Dhl\ShippingCore\Model\ResourceModel\RecipientStreet as RecipientStreetResource;
use Magento\Framework\Model\AbstractModel;

/**
 * RecipientStreet
 *
 * @package Dhl\ShippingCore\Model
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class RecipientStreet extends AbstractModel implements RecipientStreetInterface
{
    /**
     * Initialize RecipientStreet resource model.
     */
    protected function _construct()
    {
        $this->_init(RecipientStreetResource::class);
        parent::_construct();
    }

    /**
     * Get the order address id.
     *
     * @return int|null
     */
    public function getOrderAddressId()
    {
        return $this->getData(self::ORDER_ADDRESS_ID);
    }

    /**
     * Get street name.
     *
     * @return string
     */
    public function getName(): string
    {
        return (string) $this->getData(self::NAME);
    }

    /**
     * Get street number.
     *
     * @return string
     */
    public function getNumber(): string
    {
        return (string) $this->getData(self::NUMBER);
    }

    /**
     * Get supplement.
     *
     * @return string
     */
    public function getSupplement(): string
    {
        return (string) $this->getData(self::SUPPLEMENT);
    }
}
