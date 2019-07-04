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
     * @var PackagingArrayCompositeProcessor
     */
    private $compositeArrayProcessor;

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
     * @param PackagingArrayCompositeProcessor $compositeArrayProcessor
     * @param PackagingDataCompositeProcessor $compositeProcessor
     * @param ShippingDataHydrator $shippingDataHydrator
     */
    public function __construct(
        ReaderInterface $reader,
        PackagingArrayCompositeProcessor $compositeArrayProcessor,
        PackagingDataCompositeProcessor $compositeProcessor,
        ShippingDataHydrator $shippingDataHydrator
    ) {
        $this->reader = $reader;
        $this->compositeArrayProcessor = $compositeArrayProcessor;
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
        $packagingDataArray = $this->compositeArrayProcessor->processShippingOptions($packagingDataArray, $shipment);

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
            $carrier->setCompatibilityData(
                $this->compositeProcessor->processCompatibilityData(
                    $carrier->getCompatibilityData(),
                    $shipment
                )
            );

            // metadata is optional
            if ($carrier->getMetadata()) {
                $carrier->setMetadata(
                    $this->compositeProcessor->processMetadata(
                        $carrier->getMetadata(),
                        $shipment
                    )
                );
            }
        }

        return $packagingData;
    }
}
