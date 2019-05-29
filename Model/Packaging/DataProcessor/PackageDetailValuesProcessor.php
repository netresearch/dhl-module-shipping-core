<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Model\Config\CoreConfigInterface;
use Dhl\ShippingCore\Model\Packaging\AbstractProcessor;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackageDetailValuesProcessor
 *
 * @package Dhl\ShippingCore\Model\Packaging\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackageDetailValuesProcessor extends AbstractProcessor
{
    /**
     * @var CoreConfigInterface
     */
    private $coreConfig;

    /**
     * PackageDetailValuesProcessor constructor.
     *
     * @param CoreConfigInterface $coreConfig
     */
    public function __construct(CoreConfigInterface $coreConfig)
    {
        $this->coreConfig = $coreConfig;
    }

    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_PACKAGE) {
            return $optionsData;
        }

        $optionsData['packageWeight']['inputs']['weight']['defaultValue'] = $shipment->getTotalWeight() ?? 0.0;
        $optionsData['packageWeight']['inputs']['weightUnit']['defaultValue'] = $this->coreConfig->getWeightUnit($shipment->getStoreId());
        $ownPackage = $this->coreConfig->getOwnPackagesDefault($shipment->getStoreId());
        if ($ownPackage !== null) {
            $optionsData['packageSize']['inputs']['width']['defaultValue'] = $ownPackage->getWidth();
            $optionsData['packageSize']['inputs']['height']['defaultValue'] = $ownPackage->getHeight();
            $optionsData['packageSize']['inputs']['length']['defaultValue'] = $ownPackage->getLength();
        }
        $optionsData['packageSize']['inputs']['sizeUnit']['defaultValue'] = $this->coreConfig->getDimensionsUOM();
        $optionsData['packageCustoms']['inputs']['packageExportDescription']['defaultValue'] = $this->getPackageDescription($shipment);

        return $optionsData;
    }

    /**
     * @param Shipment $shipment
     * @return bool|string
     */
    private function getPackageDescription(Shipment $shipment)
    {
        $exportDescriptions = [];
        foreach ($shipment->getItems() as $item) {
            $exportDescriptions[] = $item->getDescription();
        }
        $packageExportDescription = substr(
            implode(' ', $exportDescriptions),
            0,
            80
        );
        return $packageExportDescription;
    }
}
