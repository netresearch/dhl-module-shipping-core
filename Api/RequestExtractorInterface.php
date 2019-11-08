<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api;

use Dhl\ShippingCore\Api\Data\ShipmentRequest\PackageInterface;
use Dhl\ShippingCore\Api\Data\ShipmentRequest\PackageItemInterface;
use Dhl\ShippingCore\Api\Data\ShipmentRequest\RecipientInterface;
use Dhl\ShippingCore\Api\Data\ShipmentRequest\ShipperInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class RequestExtractor
 *
 * The original shipment request is a rather limited DTO with unstructured data (DataObject, array).
 * The extractor and its subtypes offer a well-defined interface to extract the request data and
 * isolates the toxic part of extracting unstructured array data from the shipment request.
 *
 * @api
 * @package Dhl\ShippingCore\Api
 */
interface RequestExtractorInterface
{
    /**
     * Check if the given shipment request represents a return shipment.
     *
     * @return bool
     */
    public function isReturnShipmentRequest(): bool;

    /**
     * Extract the store ID as assigned to the current shipment (where the order was initially placed).
     *
     * @return int
     */
    public function getStoreId(): int;

    /**
     * Extract the base currency for the current shipment's store.
     *
     * @return string
     */
    public function getBaseCurrencyCode(): string;

    /**
     * Extract order from shipment request.
     *
     * @return Order
     */
    public function getOrder(): Order;

    /**
     * Extract shipment from shipment request.
     *
     * @return Shipment
     */
    public function getShipment(): Shipment;

    /**
     * Extract shipper from shipment request.
     *
     * @return ShipperInterface
     */
    public function getShipper(): ShipperInterface;

    /**
     * Extract recipient from shipment request.
     *
     * @return RecipientInterface
     */
    public function getRecipient(): RecipientInterface;

    /**
     * Extract package weight from shipment request.
     *
     * @return float
     */
    public function getPackageWeight(): float;

    /**
     * Extract packages from shipment request.
     *
     * @return PackageInterface[]
     * @throws LocalizedException
     */
    public function getPackages(): array;

    /**
     * Obtain all items from all packages.
     *
     * @return PackageItemInterface[]
     */
    public function getAllItems(): array;

    /**
     * Obtain all items for the current package.
     *
     * @return PackageItemInterface[]
     */
    public function getPackageItems(): array;

    /**
     * Check if "cash on delivery" was chosen for the current shipment request.
     *
     * @return bool
     */
    public function isCashOnDelivery(): bool;

    /**
     * Obtain shipment date.
     *
     * @return \DateTime
     * @throws \RuntimeException
     */
    public function getShipmentDate(): \DateTime;
}
