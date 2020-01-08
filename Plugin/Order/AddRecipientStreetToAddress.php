<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Api\OrderAddressRepositoryInterface;
use Magento\Sales\Model\Order\Address;

/**
 * Class AddRecipientStreetToEntity
 *
 * DHL uses street name and street number as separate fields. These fields are persisted as
 * extension attributes to the shipping address. In cases where a single entity gets loaded
 * through the repository, additional fields are added here.
 *
 * For loading a list of addresses see `AddRecipientStreetToAddressCollection`.
 *
 * @author  Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class AddRecipientStreetToAddress
{
    /**
     * @var OrderAddressExtensionFactory
     */
    private $orderAddressExtensionFactory;

    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * AddRecipientStreetToEntity constructor.
     *
     * @param OrderAddressExtensionFactory $orderAddressExtensionFactory
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param JoinProcessorInterface $extensionAttributesJoinProcessor
     */
    public function __construct(
        OrderAddressExtensionFactory $orderAddressExtensionFactory,
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        JoinProcessorInterface $extensionAttributesJoinProcessor
    ) {
        $this->orderAddressExtensionFactory = $orderAddressExtensionFactory;
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
    }

    /**
     * Add scalar types from our table as extension attributes.
     *
     * @param OrderAddressRepositoryInterface $repository
     * @param OrderAddressInterface $orderAddress
     * @return OrderAddressInterface
     */
    public function afterGet(
        OrderAddressRepositoryInterface $repository,
        OrderAddressInterface $orderAddress
    ): OrderAddressInterface {
        if ($orderAddress->getAddressType() !== Address::TYPE_SHIPPING) {
            // no need to handle billing addresses
            return $orderAddress;
        }

        $extensionAttributes = $orderAddress->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderAddressExtensionFactory->create();
        }

        try {
            $recipientStreet = $this->recipientStreetRepository->get((int) $orderAddress->getEntityId());
            $extensionAttributes->setDhlgwStreetName($recipientStreet->getName());
            $extensionAttributes->setDhlgwStreetNumber($recipientStreet->getNumber());
            $extensionAttributes->setDhlgwStreetSupplement($recipientStreet->getSupplement());
        } catch (\Exception $e) {
            $extensionAttributes->setDhlgwStreetName(null);
            $extensionAttributes->setDhlgwStreetNumber(null);
            $extensionAttributes->setDhlgwStreetSupplement(null);
        }

        $orderAddress->setExtensionAttributes($extensionAttributes);

        return $orderAddress;
    }
}
