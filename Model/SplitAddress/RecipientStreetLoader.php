<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\SplitAddress;

use Dhl\ShippingCore\Api\Data\RecipientStreetInterface;
use Dhl\ShippingCore\Api\Data\RecipientStreetInterfaceFactory;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetLoaderInterface;
use Dhl\ShippingCore\Api\SplitAddress\RecipientStreetRepositoryInterface;
use Dhl\ShippingCore\Model\Util\StreetSplitter;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Api\Data\OrderAddressInterface;

/**
 * Load a DHL recipient street by given address.
 */
class RecipientStreetLoader implements RecipientStreetLoaderInterface
{
    /**
     * @var RecipientStreetRepositoryInterface
     */
    private $recipientStreetRepository;

    /**
     * @var RecipientStreetInterfaceFactory
     */
    private $recipientStreetFactory;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    /**
     * RecipientStreetLoader constructor.
     *
     * @param RecipientStreetRepositoryInterface $recipientStreetRepository
     * @param RecipientStreetInterfaceFactory $recipientStreetFactory
     * @param StreetSplitter $streetSplitter
     */
    public function __construct(
        RecipientStreetRepositoryInterface $recipientStreetRepository,
        RecipientStreetInterfaceFactory $recipientStreetFactory,
        StreetSplitter $streetSplitter
    ) {
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->recipientStreetFactory = $recipientStreetFactory;
        $this->streetSplitter = $streetSplitter;
    }

    public function load(OrderAddressInterface $address): RecipientStreetInterface
    {
        try {
            $recipientStreet = $this->recipientStreetRepository->get((int)$address->getEntityId());
        } catch (NoSuchEntityException $exception) {
            $street = implode(', ', $address->getStreet());
            $addressParts = $this->streetSplitter->splitStreet($street);

            /** @var RecipientStreet $recipientStreet */
            $recipientStreet = $this->recipientStreetFactory->create();

            // set data explicitly to switch isObjectNew flag
            $recipientStreet->setData([
                RecipientStreetInterface::ORDER_ADDRESS_ID => $address->getEntityId(),
                RecipientStreetInterface::NAME => $addressParts['street_name'],
                RecipientStreetInterface::NUMBER => $addressParts['street_number'],
                RecipientStreetInterface::SUPPLEMENT => $addressParts['supplement'],
            ]);
        }

        return $recipientStreet;
    }
}
