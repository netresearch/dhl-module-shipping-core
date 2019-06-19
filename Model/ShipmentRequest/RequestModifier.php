<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\RequestModifierInterface;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
use Dhl\ShippingCore\Model\Attribute\Backend\TariffNumber;
use Dhl\ShippingCore\Model\Attribute\Source\DGCategory;
use Dhl\ShippingCore\Model\ProductData;
use Magento\Directory\Model\RegionFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\DataObjectFactory;
use Magento\Sales\Model\Order\Shipment;
use Magento\Shipping\Model\Shipment\Request;
use Magento\Store\Model\ScopeInterface;

/**
 * Class RequestModifier
 * @package Dhl\ShippingCore\Model\ShipmentRequest
 */
class RequestModifier implements RequestModifierInterface
{
    /**
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var RegionFactory
     */
    private $regionFactory;

    /**
     * @var DataObjectFactory
     */
    private $dataObjectFactory;

    /**
     * @var ProductData
     */
    private $productData;

    /**
     * RequestModifier constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ConfigInterface $config
     * @param RegionFactory $regionFactory
     * @param DataObjectFactory $dataObjectFactory
     * @param ProductData $productData
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        ConfigInterface $config,
        RegionFactory $regionFactory,
        DataObjectFactory $dataObjectFactory,
        ProductData $productData
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->config = $config;
        $this->regionFactory = $regionFactory;
        $this->dataObjectFactory = $dataObjectFactory;
        $this->productData = $productData;
    }

    /**
     * Modify / add shipment request params
     *
     * @param Request $shipmentRequest
     * @return Request
     */
    public function modify(Request $shipmentRequest): Request
    {
        $orderShipment = $shipmentRequest->getOrderShipment();
        $order = $orderShipment->getOrder();
        $storeId = $orderShipment->getStoreId();
        $baseCurrencyCode = $order->getBaseCurrencyCode();
        $shippingMethod = $order->getShippingMethod(true)->getData('method');
        $shipmentRequest->setShippingMethod($shippingMethod);
        $shipmentRequest->setBaseCurrencyCode($baseCurrencyCode);
        $shipmentRequest->setStoreId($storeId);

        $this->addReceiverData($shipmentRequest);
        $this->addShipperData($shipmentRequest);
        $this->preparePackage($shipmentRequest);

        return $shipmentRequest;
    }

    /**
     * Add shipper data to shipment request
     *
     * @param Request $shipmentRequest
     */
    private function addShipperData(Request $shipmentRequest)
    {
        $storeId = $shipmentRequest->getOrderShipment()->getStoreId();
        $originStreet = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS1,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $originStreet2 = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_ADDRESS2,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );

        $storeInfo = (array)$this->scopeConfig->getValue(
            'general/store_information',
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        $storeInfo = $this->dataObjectFactory->create(['data' => $storeInfo]);

        $shipperRegionCode = $this->scopeConfig->getValue(
            Shipment::XML_PATH_STORE_REGION_ID,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
        if (is_numeric($shipperRegionCode)) {
            $shipperRegionCode = $this->regionFactory->create()->load($shipperRegionCode)->getCode();
        }

        $shipmentRequest->setShipperContactPersonName('');
        $shipmentRequest->setShipperContactPersonFirstName('');
        $shipmentRequest->setShipperContactPersonLastName('');
        $shipmentRequest->setShipperContactCompanyName($storeInfo->getName());
        $shipmentRequest->setShipperContactPhoneNumber($storeInfo->getPhone());
        $shipmentRequest->setShipperEmail('');
        $shipmentRequest->setShipperAddressStreet(trim($originStreet . ' ' . $originStreet2));
        $shipmentRequest->setShipperAddressStreet1($originStreet);
        $shipmentRequest->setShipperAddressStreet2($originStreet2);
        $shipmentRequest->setShipperAddressCity(
            (string)$this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_CITY,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );

        $shipmentRequest->setShipperAddressStateOrProvinceCode($shipperRegionCode);
        $shipmentRequest->setShipperAddressPostalCode(
            $this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_ZIP,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
        $shipmentRequest->setShipperAddressCountryCode(
            (string)$this->scopeConfig->getValue(
                Shipment::XML_PATH_STORE_COUNTRY_ID,
                ScopeInterface::SCOPE_STORE,
                $storeId
            )
        );
    }

    /**
     * Add reciever data to shipment request
     *
     * @param Request $shipmentRequest
     */
    private function addReceiverData(Request $shipmentRequest)
    {
        $address = $shipmentRequest->getOrderShipment()->getShippingAddress();
        $personName = trim($address->getFirstname() . ' ' . $address->getLastname());
        $addressStreet = trim($address->getStreetLine(1) . ' ' . $address->getStreetLine(2));
        $region = $address->getRegionCode() ? $address->getRegionCode() : $address->getRegion();

        $shipmentRequest->setRecipientContactPersonName((string)$personName);
        $shipmentRequest->setRecipientContactPersonFirstName((string)$address->getFirstname());
        $shipmentRequest->setRecipientContactPersonLastName((string)$address->getLastname());
        $shipmentRequest->setRecipientContactCompanyName((string)$address->getCompany());
        $shipmentRequest->setRecipientContactPhoneNumber((string)$address->getTelephone());
        $shipmentRequest->setRecipientEmail((string)$address->getEmail());
        $shipmentRequest->setRecipientAddressStreet((string)$addressStreet);
        $shipmentRequest->setRecipientAddressStreet1((string)$address->getStreetLine(1));
        $shipmentRequest->setRecipientAddressStreet2((string)$address->getStreetLine(2));
        $shipmentRequest->setRecipientAddressCity((string)$address->getCity());
        $shipmentRequest->setRecipientAddressStateOrProvinceCode((string)$region);
        $shipmentRequest->setRecipientAddressRegionCode($address->getRegionCode());
        // core expects int but this would end in false postcode begin with 0
        $shipmentRequest->setRecipientAddressPostalCode($address->getPostcode());
        $shipmentRequest->setRecipientAddressCountryCode((string)$address->getCountryId());
    }

    /**
     * add package data to shipment request
     *
     * @param Request $shipmentRequest
     */
    private function preparePackage(Request $shipmentRequest)
    {
        $storeId = $shipmentRequest->getOrderShipment()->getStoreId();
        $totalWeight = 0;
        $packageValue = 0;
        $productIds = [];
        $orderItemIds = [];
        $package = [
            'params' => [],
            'items' => [],
        ];

        /** @var \Magento\Sales\Model\Order\Shipment\Item $item */
        foreach ($shipmentRequest->getOrderShipment()->getAllItems() as $item) {
            if ($item->getOrderItem()->isDummy(true)) {
                continue;
            }
            $itemData = $item->toArray(['qty', 'price', 'name', 'weight', 'product_id', 'order_item_id']);
            $itemData['customs_value'] = $item->getPrice();
            $package['items'][$item->getOrderItemId()] = $itemData;
            $totalWeight += $item->getWeight() * $item->getQty();
            $packageValue += $item->getPrice();
            $productIds[$item->getOrderItemId()] = $item->getProductId();
            $orderItemIds[$item->getOrderItemId()] = $item->getProductId();
        }

        $productData = $this->productData->getProductData($productIds, $storeId);

        foreach ($orderItemIds as $itemId => $productId) {
            $package['items'][$itemId]['description'] = $productData[$productId][ExportDescription::CODE] ?? '';
            $package['items'][$itemId]['hsCode'] = $productData[$productId][TariffNumber::CODE] ?? '';
            $package['items'][$itemId][DGCategory::CODE] = $productData[$productId][DGCategory::CODE] ?? '';
            $package['items'][$itemId]['countryOfOrigin'] = $productData[$productId]['countryOfOrigin'] ?? '';
        }

        $weightUnit = $this->config->getRawWeightUnit((string)$storeId);

        $dimensionUnit = $this->config->getRawDimensionUnit($weightUnit);

        $package['params']['container'] = '';
        $package['params']['weight'] = $totalWeight;
        $package['params']['customs_value'] = $packageValue;
        $package['params']['length'] = '';
        $package['params']['width'] = '';
        $package['params']['height'] = '';
        $package['params']['weight_units'] = $weightUnit;
        $package['params']['dimension_units'] = $dimensionUnit;
        $package['params']['content_type'] = '';
        $package['params']['content_type_other'] = '';

        $packages = [1 => $package];
        $shipmentRequest->setData('packages', $packages);
        $shipmentRequest->setData('package_id', 1);
        $shipmentRequest->setData('package_items', $package['items']);
        $shipmentRequest->setData('package_params', $package['params']);
        $shipmentRequest->getOrderShipment()->setPackages($packages);
    }
}
