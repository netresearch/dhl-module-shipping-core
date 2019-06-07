<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
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

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param Shipment $shipment
     * @param string $optionGroupName
     *
     * @return ShippingOptionInterface[]
     */
    public function processShippingOptions(array $optionsData, Shipment $shipment, string $optionGroupName): array
    {
        if ($optionGroupName !== PackagingDataProvider::GROUP_PACKAGE) {
            return $optionsData;
        }

        foreach ($optionsData as $shippingOption) {
            if ($shippingOption->getCode() === 'packageWeight') {
                $this->setPackageWeigthDefaults($shipment, $shippingOption);
            } elseif ($shippingOption->getCode() === 'packageSize') {
                $this->setPackageSizeDefaults($shipment, $shippingOption);
            } elseif ($shippingOption->getCode() === 'packageCustoms') {
                $this->setPackageCustomsDefaults($shipment, $shippingOption);
            }
        }

        return $optionsData;
    }

    /**
     * @param Shipment $shipment
     * @param ShippingOptionInterface $shippingOption
     */
    private function setPackageWeigthDefaults(
        Shipment $shipment,
        ShippingOptionInterface $shippingOption
    ) {
        foreach ($shippingOption->getInputs() as $input) {
            if ($input->getCode() === 'weight') {
                $input->setDefaultValue((string)$shipment->getTotalWeight());
            } elseif ($input->getCode() === 'weightUnit') {
                $input->setDefaultValue($this->coreConfig->getWeightUnit($shipment->getStoreId()));
            }
        }
    }

    /**
     * @param Shipment $shipment
     * @param ShippingOptionInterface $shippingOption
     */
    private function setPackageSizeDefaults(
        Shipment $shipment,
        ShippingOptionInterface $shippingOption
    ) {
        $ownPackage = $this->coreConfig->getOwnPackagesDefault((string)$shipment->getStoreId());
        foreach ($shippingOption->getInputs() as $input) {
            if ($ownPackage) {
                if ($input->getCode() === 'width') {
                    $input->setDefaultValue((string)$ownPackage->getWidth());
                } elseif ($input->getCode() === 'height') {
                    $input->setDefaultValue((string)$ownPackage->getHeight());
                } elseif ($input->getCode() === 'length') {
                    $input->setDefaultValue((string)$ownPackage->getLength());
                }
            }
            if ($input->getCode() === 'sizeUnit') {
                $input->setDefaultValue($this->coreConfig->getDimensionsUOM());
            }
        }
    }

    /**
     * @param Shipment $shipment
     * @param ShippingOptionInterface $shippingOption
     */
    private function setPackageCustomsDefaults(Shipment $shipment, ShippingOptionInterface $shippingOption)
    {
        foreach ($shippingOption->getInputs() as $input) {
            if ($input->getCode() === 'packageExportDescription') {
                $input->setDefaultValue($this->getPackageDescription($shipment));
            }
        }
    }

    /**
     * @param Shipment $shipment
     *
     * @return string
     */
    private function getPackageDescription(Shipment $shipment): string
    {
        $exportDescriptions = [];
        foreach ($shipment->getItems() as $item) {
            if ($item instanceof Shipment\Item && $product = $item->getOrderItem()->getProduct()) {
                if ($exportDescription = $product->getCustomAttribute(ExportDescription::CODE)) {
                    $exportDescriptions[] = $exportDescription->getValue();
                } else {
                    $exportDescriptions[] = $item->getDescription() ?? $item->getName();
                }
            }
        }
        $packageExportDescription = (string)substr(
            implode(' ', $exportDescriptions),
            0,
            80
        );

        return $packageExportDescription;
    }
}
