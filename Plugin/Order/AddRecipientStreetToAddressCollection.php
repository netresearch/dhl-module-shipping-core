<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\ResourceModel\Order\Address\Collection;

/**
 * Class AddRecipientStreetToAddressCollection
 *
 * DHL uses street name and street number as separate fields. These fields are persisted as
 * extension attributes to the shipping address. Magento does not load the address through
 * the repository but through the order's address collection. Hence we need to intercept
 * address collection loading to have the additional fields added.
 *
 * For loading a single address see `AddRecipientStreetToAddress`.
 *
 * @package Dhl\ShippingCore\Plugin
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class AddRecipientStreetToAddressCollection
{
    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * AddRecipientStreetToAddressCollection constructor.
     *
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(JoinProcessorInterface $extensionAttributesJoinProcessor)
    {
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Add extension attributes processing but do not alter arguments of original method.
     *
     * @param Collection $addressCollection
     * @return null
     */
    public function beforeLoadWithFilter(Collection $addressCollection)
    {
        $this->extensionAttributesJoinProcessor->process($addressCollection, Address::class);
        return null;
    }
}
