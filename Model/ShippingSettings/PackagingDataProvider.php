<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings;

use Dhl\ShippingCore\Api\Data\ShippingSettings\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingDataInterface;
use Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\ArrayProcessor\PackagingArrayCompositeProcessor;
use Dhl\ShippingCore\Model\ShippingSettings\Processor\Packaging\PackagingDataCompositeProcessor;
use Magento\Framework\Config\ReaderInterface;
use Magento\Sales\Api\Data\ShipmentInterface;

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
     * @var PackagingArrayCompositeProcessor
     */
    private $compositeArrayProcessor;

    /**
     * @var PackagingDataCompositeProcessor
     */
    private $compositeDataProcessor;

    /**
     * @var ShippingDataHydrator
     */
    private $shippingDataHydrator;

    /**
     * @var ShippingDataInterface[]
     */
    private $shipmentData = [];

    /**
     * PackagingDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param PackagingArrayCompositeProcessor $compositeArrayProcessor
     * @param PackagingDataCompositeProcessor $compositeDataProcessor
     * @param ShippingDataHydrator $shippingDataHydrator
     */
    public function __construct(
        ReaderInterface $reader,
        PackagingArrayCompositeProcessor $compositeArrayProcessor,
        PackagingDataCompositeProcessor $compositeDataProcessor,
        ShippingDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeArrayProcessor = $compositeArrayProcessor;
        $this->compositeDataProcessor = $compositeDataProcessor;
        $this->shippingDataHydrator = $shippingDataHydrator;
    }

    /**
     * @param ShipmentInterface $shipment
     *
     * @return ShippingDataInterface
     * @throws \RuntimeException
     */
    public function getData(ShipmentInterface $shipment): ShippingDataInterface
    {
        if (!empty($shipment->getEntityId()) && isset($this->shipmentData[$shipment->getEntityId()])) {
            // use cached packaging data
            return $this->shipmentData[$shipment->getEntityId()];
        }

        $packagingDataArray = $this->reader->read('adminhtml');
        $packagingDataArray = $this->compositeArrayProcessor->process($packagingDataArray, $shipment);

        $packagingData = $this->shippingDataHydrator->toObject($packagingDataArray);
        $packagingData = $this->compositeDataProcessor->process($packagingData, $shipment);

        if (!empty($shipment->getEntityId())) {
            // cache packaging data if shipment has an identifier
            $this->shipmentData[$shipment->getEntityId()] = $packagingData;
        }

        return $packagingData;
    }
}
