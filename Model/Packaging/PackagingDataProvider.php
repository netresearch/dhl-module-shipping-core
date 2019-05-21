<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Magento\Framework\Config\ReaderInterface;
use Magento\Sales\Api\Data\OrderInterface;

/**
 * Class PackagingDataProvider
 *
 * @package Dhl\ShippingCore\Model\Packaging
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class PackagingDataProvider
{
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
     * @param OrderInterface $order
     * @param int $storeId
     * @return mixed[]
     */
    public function getData(OrderInterface $order, int $storeId = null): array
    {
        $packagingData = $this->reader->read();

        foreach ($packagingData['carriers'] as $carrierCode => $carrierData) {
            foreach (['packageLevelOptions', 'itemLevelOptions'] as $group) {
                $carrierData[$group] = $this->compositeProcessor->processShippingOptions(
                    $carrierData[$group] ?? [],
                    $order,
                    $storeId
                );
            }
            $carrierData['metaData'] = $this->compositeProcessor->processMetadata(
                $carrierData['metaData'] ?? [],
                $order,
                $storeId
            );
            $carrierData['compatibilityData'] = $this->compositeProcessor->processCompatibilityData(
                $carrierData['compatibilityData'] ?? [],
                $order,
                $storeId
            );

            $packagingData['carriers'][$carrierCode] = $carrierData;
        }

        return $packagingData;
    }
}
