<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor\PackageOptions;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ValueMap\InputValueInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingOption\ValueMapInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ValueMapInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Package;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class ShippingBoxValueMapProcessor
 *
 * This class adds dynamic value mappings for the different available
 * "My own package" presets to the "Container" input
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ShippingBoxValueMapProcessor implements ShippingOptionsProcessorInterface
{
    const INPUT_CODES_MAP = [
        'weight' => 'packagingWeight',
        'width' => 'width',
        'height' => 'height',
        'length' => 'length'
    ];
    const CONTAINER_OPTION_CODE = 'packageDetails';
    const CONTAINER_INPUT_CODE = 'customPackageId';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var ValueMapInterfaceFactory
     */
    private $valueMapFactory;

    /**
     * @var InputValueInterfaceFactory
     */
    private $inputValueFactory;

    /**
     * ShippingBoxValueMapProcessor constructor.
     *
     * @param ConfigInterface $config
     * @param ValueMapInterfaceFactory $valueMapFactory
     * @param InputValueInterfaceFactory $inputValueFactory
     */
    public function __construct(
        ConfigInterface $config,
        ValueMapInterfaceFactory $valueMapFactory,
        InputValueInterfaceFactory $inputValueFactory
    ) {
        $this->config = $config;
        $this->valueMapFactory = $valueMapFactory;
        $this->inputValueFactory = $inputValueFactory;
    }

    /**
     * @param $scopeId
     *
     * @return ValueMapInterface[]
     */
    private function buildValueMaps($scopeId): array
    {
        $maps = [];
        $packages = $this->config->getOwnPackages($scopeId);

        foreach ($packages->getIterator() as $package) {
            /** @var Package $package */
            $inputValues = [];
            foreach (self::INPUT_CODES_MAP as $dataKey => $inputCode) {
                $inputValue = $this->inputValueFactory->create();
                $inputValue->setCode(self::CONTAINER_OPTION_CODE . '.' . $inputCode);
                $inputValue->setValue((string) $package->getData($dataKey));
                $inputValues[] = $inputValue;
            }

            $map = $this->valueMapFactory->create();
            $map->setSourceValue($package->getData('id'));
            $map->setInputValues($inputValues);
            $maps[] = $map;
        }

        return $maps;
    }

    /**
     * @param ShippingOptionInterface[] $optionsData
     * @param ShipmentInterface $shipment
     *
     * @return ShippingOptionInterface[]
     */
    public function process(array $optionsData, ShipmentInterface $shipment): array
    {
        if (!isset(
            $optionsData[self::CONTAINER_OPTION_CODE],
            $optionsData[self::CONTAINER_OPTION_CODE]->getInputs()[self::CONTAINER_INPUT_CODE]
        )) {
            return $optionsData;
        }

        $input = $optionsData[self::CONTAINER_OPTION_CODE]->getInputs()[self::CONTAINER_INPUT_CODE];

        $input->setValueMaps($this->buildValueMaps($shipment->getStoreId()));

        return $optionsData;
    }
}
