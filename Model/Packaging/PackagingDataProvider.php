<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

use Magento\Framework\Config\ReaderInterface;
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
     * Option group names relevant for packaging
     */
    const GROUP_NAMES = [self::GROUP_PACKAGE, self::GROUP_ITEM, self::GROUP_SERVICE];

    /**
     * @var ReaderInterface
     */
    private $reader;

    /**
     * @var PackagingDataCompositeProcessor
     */
    private $compositeProcessor;

    /**
     * PackagingDataProvider constructor.
     *
     * @param ReaderInterface $reader
     * @param PackagingDataCompositeProcessor $compositeProcessor
     */
    public function __construct(ReaderInterface $reader, PackagingDataCompositeProcessor $compositeProcessor)
    {
        $this->reader = $reader;
        $this->compositeProcessor = $compositeProcessor;
    }

    /**
     * @param Shipment $shipment
     * @return mixed[]
     */
    public function getData(Shipment $shipment): array
    {
        $packagingData = $this->reader->read('adminhtml');

        if (!isset($packagingData['carriers'])) {
            $packagingData['carriers'] = [];
        }

        $orderCarrier = strtok((string) $shipment->getOrder()->getShippingMethod(), '_');
        foreach ($packagingData['carriers'] as $carrierCode => $carrierData) {
            if ($orderCarrier !== $carrierCode) {
                unset($packagingData['carriers'][$carrierCode]);
                continue;
            }
            foreach (self::GROUP_NAMES as $group) {
                $carrierData[$group] = $this->compositeProcessor->processShippingOptions(
                    $carrierData[$group] ?? [],
                    $shipment,
                    $group
                );
            }
            $carrierData['metaData'] = $this->compositeProcessor->processMetadata(
                $carrierData['metaData'] ?? [],
                $shipment
            );
            $carrierData['compatibilityData'] = $this->compositeProcessor->processCompatibilityData(
                $carrierData['compatibilityData'] ?? [],
                $shipment
            );

            $packagingData['carriers'][$carrierCode] = $carrierData;
        }

        return $packagingData;
    }
}
