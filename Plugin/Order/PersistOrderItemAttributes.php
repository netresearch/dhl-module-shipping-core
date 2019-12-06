<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Dhl\ShippingCore\Api\Data\OrderItemAttributesInterfaceFactory;
use Dhl\ShippingCore\Model\OrderItemAttributesRepository;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Api\Data\OrderItemInterface;
use Magento\Sales\Api\OrderItemRepositoryInterface;
use Psr\Log\LoggerInterface;

/**
 * Class PersistOrderItemAttributes
 *
 * Persist our custom product attributes.
 *
 * @author Sebastian Ertner <sebastian.ertner@netresearch.de>
 * @link https://www.netresearch.de/
 */
class PersistOrderItemAttributes
{
    /**
     * @var OrderItemAttributesInterfaceFactory
     */
    private $orderItemAttributeFactory;

    /**
     * @var OrderItemAttributesRepository
     */
    private $orderItemAttributeRepository;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * PersistOrderItemAttributes constructor.
     *
     * @param OrderItemAttributesInterfaceFactory $orderItemAttributeFactory
     * @param OrderItemAttributesRepository $orderItemAttributeRepository
     * @param LoggerInterface $logger
     */
    public function __construct(
        OrderItemAttributesInterfaceFactory $orderItemAttributeFactory,
        OrderItemAttributesRepository $orderItemAttributeRepository,
        LoggerInterface $logger
    ) {
        $this->orderItemAttributeFactory = $orderItemAttributeFactory;
        $this->orderItemAttributeRepository = $orderItemAttributeRepository;
        $this->logger = $logger;
    }

    /**
     * Persist additional order item properties.
     *
     * Shift order item's extension attributes to a new attribute entity
     * and save it with reference to the original item.
     *
     * @param OrderItemRepositoryInterface $subject
     * @param OrderItemInterface $orderItem
     * @return OrderItemInterface
     */
    public function afterSave(
        OrderItemRepositoryInterface $subject,
        OrderItemInterface $orderItem
    ): OrderItemInterface {
        $extensionAttributes = $orderItem->getExtensionAttributes();
        if (!$extensionAttributes) {
            // no extension attributes where added to the item, ignore
            return $orderItem;
        }

        $countryOfManufacture = $extensionAttributes->getDhlgwCountryOfManufacture();
        $dgCategory = $extensionAttributes->getDhlgwDgCategory();
        $exportDescription = $extensionAttributes->getDhlgwExportDescription();
        $hsCode = $extensionAttributes->getDhlgwTariffNumber();

        if (!$countryOfManufacture && !$dgCategory && !$exportDescription && !$hsCode) {
            return $orderItem;
        }

        try {
            $orderItemAttribute = $this->orderItemAttributeFactory->create();
            $orderItemAttribute->setItemId((int) $orderItem->getItemId());
            $orderItemAttribute->setCountryOfManufacture($countryOfManufacture);
            $orderItemAttribute->setDgCategory($dgCategory);
            $orderItemAttribute->setExportDescription($exportDescription);
            $orderItemAttribute->setTariffNumber($hsCode);

            $this->orderItemAttributeRepository->save($orderItemAttribute);
        } catch (\Exception $ex) {
            $message = ($ex instanceof LocalizedException) ? $ex->getLogMessage() : $ex->getMessage();
            $this->logger->error($message, ['exception' => $ex]);
        }

        return $orderItem;
    }
}
