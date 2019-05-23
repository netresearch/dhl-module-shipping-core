<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging;

use Magento\Framework\Config\ReaderInterface;
use Magento\Sales\Model\Order;

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
     * @param Order $order
     * @return mixed[]
     */
    public function getData(Order $order): array
    {
        $packagingData = $this->reader->read('adminhtml');

        if (!isset($packagingData['carriers'])) {
            $packagingData['carriers'] = [];
        }

        foreach ($packagingData['carriers'] as $carrierCode => $carrierData) {
            if (strtok((string) $order->getShippingMethod(), '_') === $carrierCode) {
                unset($packagingData['carriers'][$carrierCode]);
                continue;
            }
            foreach (['packageLevelOptions', 'itemLevelOptions'] as $group) {
                $carrierData[$group] = $this->compositeProcessor->processShippingOptions(
                    $carrierData[$group] ?? [],
                    $order
                );
            }
            $carrierData['metaData'] = $this->compositeProcessor->processMetadata(
                $carrierData['metaData'] ?? [],
                $order
            );
            $carrierData['compatibilityData'] = $this->compositeProcessor->processCompatibilityData(
                $carrierData['compatibilityData'] ?? [],
                $order
            );

            $packagingData['carriers'][$carrierCode] = $carrierData;
        }

        return $packagingData;
    }
}
