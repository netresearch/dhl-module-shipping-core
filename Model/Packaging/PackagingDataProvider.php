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
    const GROUP_ITEM    = 'itemOptions';
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
     * @param Shipment $shipment
     *
     * @return ShippingDataInterface
     * @throws \RuntimeException
     */
    public function getData(Shipment $shipment): ShippingDataInterface
    {
        $packagingDataArray = $this->reader->read('adminhtml');
        $packagingDataArray = $this->compositeArrayProcessor->process($packagingDataArray, $shipment);
        $packagingData      = $this->shippingDataHydrator->toObject($packagingDataArray);

        return $this->compositeDataProcessor
            ->process($packagingData, $shipment);
    }
}
