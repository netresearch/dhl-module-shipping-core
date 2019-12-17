<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Packaging\DataProcessor\PackageOptions;

use Dhl\ShippingCore\Api\ConfigInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\OptionInterfaceFactory;
use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Packaging\ShippingOptionsProcessorInterface;
use Dhl\ShippingCore\Model\Package;
use Magento\Sales\Api\Data\ShipmentInterface;

/**
 * Class PackageContainerInputDataProcessor
 *
 * This class is hooked into the shipping option data creation via di.xml.
 * It sets dynamic options for the "Container" input in the packaging popup
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class PackageContainerInputDataProcessor implements ShippingOptionsProcessorInterface
{
    const CONTAINER_OPTION_CODE = 'packageDetails';
    const CONTAINER_INPUT_CODE = 'customPackageId';

    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var OptionInterfaceFactory
     */
    private $optionFactory;

    /**
     * PackageContainerInputDataProcessor constructor.
     *
     * @param ConfigInterface $config
     * @param CommentInterfaceFactory $commentFactory
     * @param OptionInterfaceFactory $optionFactory
     */
    public function __construct(
        ConfigInterface $config,
        CommentInterfaceFactory $commentFactory,
        OptionInterfaceFactory $optionFactory
    ) {
        $this->config = $config;
        $this->optionFactory = $optionFactory;
    }

    /**
     * Set options and default value for custom container input
     *
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

        $shippingOption = $optionsData[self::CONTAINER_OPTION_CODE];
        $customContainers = $this->config->getOwnPackages($shipment->getStoreId())->getIterator()->getArrayCopy();

        if (empty($customContainers)) {
            /**
             * If there are no custom containers configured, remove the input entirely
             */
            $inputs = $shippingOption->getInputs();
            unset($inputs[self::CONTAINER_INPUT_CODE]);
            $shippingOption->setInputs($inputs);
            return $optionsData;
        }

        $containerInput = $shippingOption->getInputs()[self::CONTAINER_INPUT_CODE];
        $this->setInputOptions($containerInput, $customContainers);
        $this->setDefaultContainer($shipment, $containerInput);

        return $optionsData;
    }

    /**
     * Add options for custom containers
     *
     * @param InputInterface $containerInput
     * @param Package[] $customContainers
     */
    private function setInputOptions($containerInput, array $customContainers)
    {
        $containerInput->setOptions(
            array_map(
                function (Package $package) {
                    $option = $this->optionFactory->create();
                    $option->setValue($package->getData('id'));
                    $option->setLabel($package->getTitle());

                    return $option;
                },
                $customContainers
            )
        );
    }

    /**
     * Set default container as default input value
     *
     * @param ShipmentInterface $shipment
     * @param InputInterface $containerInput
     */
    private function setDefaultContainer(ShipmentInterface $shipment, InputInterface $containerInput)
    {
        $defaultContainer = $this->config->getOwnPackagesDefault($shipment->getStoreId());
        if ($defaultContainer) {
            $containerInput->setDefaultValue(
                $defaultContainer->getData('id')
            );
        }
    }
}
