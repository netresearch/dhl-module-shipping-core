<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;
use Dhl\ShippingCore\Model\Attribute\Backend\ExportDescription;
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
     * @var ConfigInterface
     */
    private $config;

    /**
     * PackageDetailValuesProcessor constructor.
     *
     * @param ConfigInterface $config
     */
    public function __construct(ConfigInterface $config)
    {
        $this->config = $config;
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
                $this->setPackageWeightDefaults($shipment, $shippingOption);
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
    private function setPackageWeightDefaults(
        Shipment $shipment,
        ShippingOptionInterface $shippingOption
    ) {
        foreach ($shippingOption->getInputs() as $input) {
            if ($input->getCode() === 'weight') {
                $input->setDefaultValue((string) $this->getTotalWeight($shipment));
            } elseif ($input->getCode() === 'weightUnit') {
                $input->setDefaultValue($this->config->getRawWeightUnit($shipment->getStoreId()));
            }
        }
    }

    /**
     * @param Shipment $shipment
     * @return float|int
     */
    private function getTotalWeight(Shipment $shipment)
    {
        $weight = 0.0;
        /** @var Shipment\Item $item */
        foreach ($shipment->getAllItems() as $item) {
            $weight += $item->getWeight() * $item->getQty();
        }

        return $weight;
    }

    /**
     * @param Shipment $shipment
     * @param ShippingOptionInterface $shippingOption
     */
    private function setPackageSizeDefaults(
        Shipment $shipment,
        ShippingOptionInterface $shippingOption
    ) {
        $ownPackage = $this->config->getOwnPackagesDefault((string) $shipment->getStoreId());

        foreach ($shippingOption->getInputs() as $input) {
            if ($ownPackage) {
                if ($input->getCode() === 'width') {
                    $input->setDefaultValue((string) $ownPackage->getWidth());
                } elseif ($input->getCode() === 'height') {
                    $input->setDefaultValue((string) $ownPackage->getHeight());
                } elseif ($input->getCode() === 'length') {
                    $input->setDefaultValue((string) $ownPackage->getLength());
                }
            }

            if ($input->getCode() === 'sizeUnit') {
                $input->setDefaultValue(
                    $this->config->getRawDimensionUnit($this->config->getRawWeightUnit($shipment->getStoreId()))
                );
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

        return substr(
            implode(' ', $exportDescriptions),
            0,
            80
        );
    }
}
