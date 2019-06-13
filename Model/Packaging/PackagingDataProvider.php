<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging;

use Dhl\ShippingCore\Api\Data\ShippingDataInterface;
use Dhl\ShippingCore\Model\ShippingDataHydrator;
use Magento\Framework\Config\ReaderInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackagingDataProvider
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class PackagingDataProvider
{
    const GROUP_PACKAGE = 'packageOptions';
    const GROUP_ITEM = 'itemOptions';
    const GROUP_SERVICE = 'serviceOptions';

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var PackagingDataCompositeProcessor
     */
    private $compositeProcessor;

    /**
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * PackagingDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param PackagingDataCompositeProcessor $compositeProcessor
     * @param ShippingDataHydrator $shippingDataHydrator
     */
    public function __construct(
        ReaderInterface $reader,
        PackagingDataCompositeProcessor $compositeProcessor,
        ShippingDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeProcessor = $compositeProcessor;
        $this->shippingDataHydrator = $shippingDataHydrator;
    }

    /**
     * @param Shipment $shipment
     *
     * @return ShippingDataInterface
     * @throws LocalizedException
     */
    public function getData(Shipment $shipment): ShippingDataInterface
    {
        $packagingDataArray = $this->reader->read('adminhtml');
        $packagingDataArray = $this->filterCarriers($shipment, $packagingDataArray);
        $packagingDataArray = $this->cloneItemTemplates($shipment, $packagingDataArray);
        $packagingData = $this->shippingDataHydrator->toObject($packagingDataArray);

        foreach ($packagingData->getCarriers() as $index => $carrier) {
            $carrier->setPackageOptions(
                $this->compositeProcessor->processShippingOptions(
                    $carrier->getPackageOptions(),
                    $shipment,
                    self::GROUP_PACKAGE
                )
            );
            $carrier->setServiceOptions(
                $this->compositeProcessor->processShippingOptions(
                    $carrier->getServiceOptions(),
                    $shipment,
                    self::GROUP_SERVICE
                )
            );
            $carrier->setItemOptions(
                $this->compositeProcessor->processItemOptions(
                    $carrier->getItemOptions(),
                    $shipment
                )
            );
            $carrier->setMetadata(
                $this->compositeProcessor->processMetadata(
                    $carrier->getMetadata(),
                    $shipment
                )
            );
            $carrier->setCompatibilityData(
                $this->compositeProcessor->processCompatibilityData(
                    $carrier->getCompatibilityData(),
                    $shipment
                )
            );
        }

        return $packagingData;
    }

    /**
     * Remove all carrier data that does not match the given shipment.
     *
     * @param Shipment $shipment
     * @param array $packagingDataArray
     * @return array
     */
    private function filterCarriers(Shipment $shipment, array $packagingDataArray): array
    {
        $orderCarrier = strtok((string)$shipment->getOrder()->getShippingMethod(), '_');
        $packagingDataArray['carriers'] = array_filter(
            $packagingDataArray['carriers'],
            function (array $carrier) use ($orderCarrier) {
                return $carrier['code'] === $orderCarrier;
            }
        );
        return $packagingDataArray;
    }

    /**
     * Convert the static ItemShippingOption array read from xml
     * into separate elements for each shipment item.
     *
     * @param Shipment $shipment
     * @param array $packagingDataArray
     * @return array
     */
    private function cloneItemTemplates(Shipment $shipment, array $packagingDataArray): array
    {
        foreach ($packagingDataArray['carriers'] as $carrierCode => $carrier) {
            $newData = [];
            foreach ($shipment->getItems() as $item) {
                $itemId = (int)$item->getOrderItemId();
                $newItem = [
                    'itemId' => $itemId,
                    'shippingOptions' => [],
                ];
                foreach ($carrier['itemOptions'] as $itemOptions) {
                    $newItem['shippingOptions'] +=  $itemOptions['shippingOptions'];
                }
                $newData[$itemId] = $newItem;
            }
            $packagingDataArray['carriers'][$carrierCode]['itemOptions'] = $newData;
        }

        return $packagingDataArray;
    }
}
