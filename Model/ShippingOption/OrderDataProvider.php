<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\CarrierDataInterface;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order;
use Magento\Sales\Model\Order\ShipmentFactory;

/**
 * Class OrderDataProvider
 *
 * Allows pregeneration expected shipping options for an order
 *
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @link https://www.netresearch.de/
 */
class OrderDataProvider
{
    /**
     * @var PackagingDataProvider
     */
    private $packageDataProvider;

    /**
     * @var ShipmentFactory
     */
    private $shipmentFactory;

    /**
     * OrderDataProvider constructor.
     *
     * @param PackagingDataProvider $packageDataProvider
     * @param ShipmentFactory $shipmentFactory
     */
    public function __construct(PackagingDataProvider $packageDataProvider, ShipmentFactory $shipmentFactory)
    {
        $this->packageDataProvider = $packageDataProvider;
        $this->shipmentFactory = $shipmentFactory;
    }

    /**
     * @param Order $order
     * @return CarrierDataInterface|null
     */
    public function getShippingOptions(Order $order)
    {
        /** need to create a tmp shipment for packagingDataProvider */
        try {
            /** @var Order\Shipment $shipment */
            $shipment = $this->shipmentFactory->create($order);
            $packagingData = $this->packageDataProvider->getData($shipment);
        } catch (LocalizedException $e) {
            return null;
        }
        $carrierCode = strtok((string) $order->getShippingMethod(), '_');
        $carrierData = $packagingData->getCarriers();

        $carrierData = array_filter(
            $carrierData,
            static function (CarrierDataInterface $carrierData) use ($carrierCode) {
                return $carrierData->getCode() === $carrierCode;
            }
        );

        return array_pop($carrierData);
    }
}
