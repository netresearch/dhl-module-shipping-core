<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Pipeline\Shipment\ShipmentRequest;

use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageAdditionalInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\PackageItemInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\RecipientInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\RecipientInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterface;
use Dhl\ShippingCore\Api\Data\Pipeline\ShipmentRequest\ShipperInterfaceFactory;
use Dhl\ShippingCore\Api\Pipeline\ShipmentRequest\RequestExtractorInterface;
use Dhl\ShippingCore\Model\ShipmentDate\ShipmentDate;
use Dhl\ShippingCore\Model\SplitAddress\RecipientStreetRepository;
use Dhl\ShippingCore\Model\Util\StreetSplitter;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipment\Request;

/**
 * Request Extractor
 *
 * The original shipment request is a rather limited DTO with unstructured data (DataObject, array).
 * The extractor and its subtypes offer a well-defined interface to extract the request data and
 * isolates the toxic part of extracting unstructured array data from the shipment request.
 */
class RequestExtractor implements RequestExtractorInterface
{
    /**
     * @var Request
     */
    private $shipmentRequest;

    /**
     * @var StreetSplitter
     */
    private $streetSplitter;

    /**
     * @var RecipientStreetRepository
     */
    private $recipientStreetRepository;

    /**
     * @var ShipperInterfaceFactory
     */
    private $shipperFactory;

    /**
     * @var RecipientInterfaceFactory
     */
    private $recipientFactory;

    /**
     * @var PackageInterfaceFactory
     */
    private $packageFactory;

    /**
     * @var PackageAdditionalInterfaceFactory
     */
    private $packageAdditionalFactory;

    /**
     * @var PackageItemInterfaceFactory
     */
    private $packageItemFactory;

    /**
     * @var ShipperInterface
     */
    private $shipper;

    /**
     * @var RecipientInterface
     */
    private $recipient;

    /**
     * @var PackageInterface[]
     */
    private $packages;

    /**
     * @var PackageItemInterface[]
     */
    private $packageItems;

    /**
     * @var ShipmentDate
     */
    private $shipmentDate;

    /**
     * RequestExtractor constructor.
     *
     * @param Request $shipmentRequest
     * @param StreetSplitter $streetSplitter
     * @param RecipientStreetRepository $recipientStreetRepository
     * @param ShipperInterfaceFactory $shipperFactory
     * @param RecipientInterfaceFactory $recipientFactory
     * @param PackageInterfaceFactory $packageFactory
     * @param PackageAdditionalInterfaceFactory $packageAdditionalFactory
     * @param PackageItemInterfaceFactory $packageItemFactory
     * @param ShipmentDate $shipmentDate
     */
    public function __construct(
        Request $shipmentRequest,
        StreetSplitter $streetSplitter,
        RecipientStreetRepository $recipientStreetRepository,
        ShipperInterfaceFactory $shipperFactory,
        RecipientInterfaceFactory $recipientFactory,
        PackageInterfaceFactory $packageFactory,
        PackageAdditionalInterfaceFactory $packageAdditionalFactory,
        PackageItemInterfaceFactory $packageItemFactory,
        ShipmentDate $shipmentDate
    ) {
        $this->shipmentRequest = $shipmentRequest;
        $this->streetSplitter = $streetSplitter;
        $this->recipientStreetRepository = $recipientStreetRepository;
        $this->shipperFactory = $shipperFactory;
        $this->recipientFactory = $recipientFactory;
        $this->packageFactory = $packageFactory;
        $this->packageAdditionalFactory = $packageAdditionalFactory;
        $this->packageItemFactory = $packageItemFactory;
        $this->shipmentDate = $shipmentDate;
    }

    /**
     * Check if the given shipment request represents a return shipment.
     *
     * @return bool
     */
    public function isReturnShipmentRequest(): bool
    {
        return (bool) $this->shipmentRequest->getData('is_return');
    }

    /**
     * Extract the store ID as assigned to the current shipment (where the order was initially placed).
     *
     * @return int
     */
    public function getStoreId(): int
    {
        return (int) $this->shipmentRequest->getData('store_id');
    }

    /**
     * Extract the base currency for the current shipment's store.
     *
     * @return string
     */
    public function getBaseCurrencyCode(): string
    {
        return (string) $this->shipmentRequest->getData('base_currency_code');
    }

    /**
     * Extract order from shipment request.
     *
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->shipmentRequest->getOrderShipment()->getOrder();
    }

    /**
     * Extract shipment from shipment request.
     *
     * @return Shipment
     */
    public function getShipment(): Shipment
    {
        return $this->shipmentRequest->getOrderShipment();
    }

    /**
     * Extract shipper from shipment request.
     *
     * @return ShipperInterface
     */
    public function getShipper(): ShipperInterface
    {
        if (empty($this->shipper)) {
            $street = (string)$this->shipmentRequest->getShipperAddressStreet();
            $streetParts = $this->streetSplitter->splitStreet($street);
            $streetData = [
                'streetName' => $streetParts['street_name'],
                'streetNumber' => $streetParts['street_number'],
                'addressAddition' => $streetParts['supplement'],
            ];

            $shipperData = [
                'contactPersonName' => (string)$this->shipmentRequest->getShipperContactPersonName(),
                'contactPersonFirstName' => (string)$this->shipmentRequest->getShipperContactPersonFirstName(),
                'contactPersonLastName' => (string)$this->shipmentRequest->getShipperContactPersonLastName(),
                'contactCompanyName' => (string)$this->shipmentRequest->getShipperContactCompanyName(),
                'contactEmail' => (string)$this->shipmentRequest->getData('shipper_email'),
                'contactPhoneNumber' => (string)$this->shipmentRequest->getShipperContactPhoneNumber(),
                'street' => [
                    $this->shipmentRequest->getShipperAddressStreet1(),
                    $this->shipmentRequest->getShipperAddressStreet2(),
                ],
                'city' => (string) $this->shipmentRequest->getShipperAddressCity(),
                'state' => (string) $this->shipmentRequest->getShipperAddressStateOrProvinceCode(),
                'postalCode' => (string) $this->shipmentRequest->getShipperAddressPostalCode(),
                'countryCode' => (string) $this->shipmentRequest->getShipperAddressCountryCode(),
            ];

            $shipperData = array_merge($shipperData, $streetData);
            $this->shipper = $this->shipperFactory->create($shipperData);
        }

        return $this->shipper;
    }

    /**
     * Extract recipient from shipment request.
     *
     * @return RecipientInterface
     */
    public function getRecipient(): RecipientInterface
    {
        if (empty($this->recipient)) {
            try {
                $shippingAddressId = (int) $this->getOrder()->getData('shipping_address_id');
                $recipientStreet = $this->recipientStreetRepository->get($shippingAddressId);
                $streetData = [
                    'streetName' => $recipientStreet->getName(),
                    'streetNumber' => $recipientStreet->getNumber(),
                    'addressAddition' => $recipientStreet->getSupplement(),
                ];
            } catch (NoSuchEntityException $exception) {
                $streetData = [
                    'streetName' => '',
                    'streetNumber' => '',
                    'addressAddition' => '',
                ];
            }

            $recipientData = [
                'contactPersonName' => (string)$this->shipmentRequest->getRecipientContactPersonName(),
                'contactPersonFirstName' => (string)$this->shipmentRequest->getRecipientContactPersonFirstName(),
                'contactPersonLastName' => (string)$this->shipmentRequest->getRecipientContactPersonLastName(),
                'contactCompanyName' => (string)$this->shipmentRequest->getRecipientContactCompanyName(),
                'contactEmail' => (string)$this->shipmentRequest->getData('recipient_email'),
                'contactPhoneNumber' => (string)$this->shipmentRequest->getRecipientContactPhoneNumber(),
                'street' => [
                    $this->shipmentRequest->getRecipientAddressStreet1(),
                    $this->shipmentRequest->getRecipientAddressStreet2(),
                ],
                'city' => (string) $this->shipmentRequest->getRecipientAddressCity(),
                'state' => (string) $this->shipmentRequest->getRecipientAddressStateOrProvinceCode(),
                'postalCode' => (string) $this->shipmentRequest->getRecipientAddressPostalCode(),
                'countryCode' => (string) $this->shipmentRequest->getRecipientAddressCountryCode(),
                'regionCode' => (string) $this->shipmentRequest->getData('recipient_address_region_code'),
            ];

            $recipientData = array_merge($recipientData, $streetData);
            $this->recipient = $this->recipientFactory->create($recipientData);
        }

        return $this->recipient;
    }

    /**
     * Extract package weight from shipment request.
     *
     * @return float
     */
    public function getPackageWeight(): float
    {
        return (float) $this->shipmentRequest->getPackageWeight();
    }

    /**
     * Extract packages from shipment request.
     *
     * @return PackageInterface[]
     * @throws LocalizedException
     */
    public function getPackages(): array
    {
        if (empty($this->packages)) {
            $this->packages = array_map(function (array $packageData) {
                $params = $packageData['params'];
                $package = $this->packageFactory->create([
                    'productCode' => $params['shipping_product'] ?? '',
                    'containerType' => $params['container'] ?? '',
                    'weightUom' => $params['weight_units'],
                    'dimensionsUom' => $params['dimension_units'],
                    'weight' => (float) $params['weight'],
                    'length' => isset($params['length']) ? (float) $params['length'] : null,
                    'width' => isset($params['width']) ? (float) $params['width'] : null,
                    'height' => isset($params['height']) ? (float) $params['height'] : null,
                    'customsValue' => isset($params['customs_value']) ? (float) $params['customs_value'] : null,
                    'exportDescription' => $params['customs']['exportDescription'] ?? '',
                    'termsOfTrade' => $params['customs']['termsOfTrade'] ?? '',
                    'contentType' => $params['content_type'] ?? '',
                    'contentExplanation' => $params['content_type_other'] ?? '',
                    'packageAdditional' => $this->packageAdditionalFactory->create(),
                ]);

                return $package;
            }, $this->shipmentRequest->getData('packages'));
        }

        $packageId = $this->shipmentRequest->getData('package_id');
        if ($packageId === null) {
            // no dedicated package requested, return all packages
            return $this->packages;
        }

        if (!isset($this->packages[$packageId])) {
            // requested package not found
            throw new LocalizedException(__('Package #%1 not found in shipment request.', $packageId));
        }

        return [$packageId => $this->packages[$packageId]];
    }

    /**
     * Obtain all items from all packages.
     *
     * @return PackageItemInterface[]
     */
    public function getAllItems(): array
    {
        if (empty($this->packageItems)) {
            $allItems = [];
            $packages = $this->shipmentRequest->getData('packages');

            foreach ($packages as $packageId => $packageData) {
                $packageItems = array_map(function (array $itemData) use ($packageId) {
                    $packageItem = $this->packageItemFactory->create([
                        'orderItemId' => (int)$itemData['order_item_id'],
                        'productId' => (int)$itemData['product_id'],
                        'packageId' => (int)$packageId,
                        'name' => $itemData['name'],
                        'qty' => (float)$itemData['qty'],
                        'weight' => (float)$itemData['weight'],
                        'price' => (float)$itemData['price'],
                        'customsValue' => isset($itemData['customs_value']) ? (float)$itemData['customs_value'] : null,
                        'sku' => $itemData['sku'] ?? '',
                        'exportDescription' => $itemData['customs']['exportDescription'] ?? '',
                        'hsCode' => $itemData['customs']['hsCode'] ?? '',
                        'countryOfOrigin' => $itemData['customs']['countryOfOrigin'] ?? '',
                    ]);

                    return $packageItem;
                }, $packageData['items']);

                $allItems[] = $packageItems;
            }

            $this->packageItems = array_merge(...$allItems);
        }

        return $this->packageItems;
    }

    /**
     * Obtain all items for the current package.
     *
     * @return PackageItemInterface[]
     */
    public function getPackageItems(): array
    {
        $packageId = $this->shipmentRequest->getData('package_id');
        $items = array_filter(
            $this->getAllItems(),
            static function (PackageItemInterface $item) use ($packageId) {
                return ($packageId === $item->getPackageId());
            }
        );

        return $items;
    }

    /**
     * Obtain shipment date. This currently does not check for holidays or weekends.
     *
     * @return \DateTime
     * @throws \RuntimeException
     */
    public function getShipmentDate(): \DateTime
    {
        return $this->shipmentDate->getDate($this->getStoreId());
    }
}
