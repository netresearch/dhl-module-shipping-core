<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order\Address;

use Dhl\ShippingCore\Api\RecipientStreetRepositoryInterface;
use Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface;
use Magento\Sales\Api\Data\OrderAddressExtensionFactory;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\AddressRepository;
use Magento\Sales\Model\ResourceModel\Order\Address\Collection;

/**
 * Class AddressRepositoryPlugin
 *
 * @package Dhl\ShippingCore\Plugin\Order\Address
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link http://www.netresearch.de/
 */
class AddressRepositoryPlugin
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
     * AddressRepositoryPlugin constructor.
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
     * @param AddressRepository $subject
     * @param OrderAddressInterface $orderAddress
     * @return OrderAddressInterface
     */
    public function afterGet(AddressRepository $subject, OrderAddressInterface $orderAddress): OrderAddressInterface
    {
        if ($orderAddress->getAddressType() !== Address::TYPE_SHIPPING) {
            // no need to handle billing addresses
            return $orderAddress;
        }

        $extensionAttributes = $orderAddress->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->orderAddressExtensionFactory->create();
        }

        try {
            $recipientStreet = $this->recipientStreetRepository->get($orderAddress->getEntityId());
            $extensionAttributes->setDhlStreetName($recipientStreet->getName());
            $extensionAttributes->setDhlStreetNumber($recipientStreet->getNumber());
            $extensionAttributes->setDhlStreetSupplement($recipientStreet->getSupplement());
        } catch (\Exception $e) {
            $extensionAttributes->setDhlStreetName(null);
            $extensionAttributes->setDhlStreetNumber(null);
            $extensionAttributes->setDhlStreetSupplement(null);
        }

        $orderAddress->setExtensionAttributes($extensionAttributes);

        return $orderAddress;
    }

    /**
     * Add our fields as extension attributes.
     *
     * @param AddressRepository $subject
     * @param Collection $collection
     * @return Collection
     */
    public function afterGetList(AddressRepository $subject,Collection $collection): Collection
    {
        $this->extensionAttributesJoinProcessor->process($collection, Address::class);
        return $collection;
    }
}
