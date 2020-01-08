<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;
use Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterfaceFactory;
use Dhl\ShippingCore\Api\Data\OrderExport\ServiceDataInterface;
use Dhl\ShippingCore\Api\Data\OrderExport\ServiceDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\OrderExport\ShippingOptionInterface;
use Dhl\ShippingCore\Api\Data\OrderExport\ShippingOptionInterfaceFactory;
use Dhl\ShippingCore\Model\AdditionalFee\TotalsManager;
use Dhl\ShippingCore\Model\Packaging\DataProcessor\PackageOptions\PackageContainerInputDataProcessor;
use Dhl\ShippingCore\Model\ShippingSettings\OrderDataProvider;
use Magento\Sales\Api\Data\ShippingExtensionFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Api\Data\TotalExtensionInterfaceFactory;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\ShippingBuilder;

/**
 * Class ShippingBuilderPlugin
 *
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ShippingBuilderPlugin
{
    /**
     * @var ShippingExtensionFactory
     */
    private $shippingExtensionFactory;

    /**
     * @var ServiceDataInterfaceFactory
     */
    private $serviceDataFactory;

    /**
     * @var ShippingOptionInterfaceFactory
     */
    private $packageDataFactory;

    /**
     * @var KeyValueObjectInterfaceFactory
     */
    private $keyValueObjectFactory;

    /**
     * @var OrderDataProvider
     */
    private $orderDataProvider;

    /**
     * @var TotalExtensionInterfaceFactory
     */
    private $totalExtensionFactory;

    /**
     * ShippingBuilderPlugin constructor.
     *
     * @param ShippingExtensionFactory $shippingExtensionFactory
     * @param ServiceDataInterfaceFactory $serviceDataFactory
     * @param ShippingOptionInterfaceFactory $packageDataFactory
     * @param KeyValueObjectInterfaceFactory $keyValueObjectFactory
     * @param OrderDataProvider $orderDataProvider
     * @param TotalExtensionInterfaceFactory $totalExtensionFactory
     */
    public function __construct(
        ShippingExtensionFactory $shippingExtensionFactory,
        ServiceDataInterfaceFactory $serviceDataFactory,
        ShippingOptionInterfaceFactory $packageDataFactory,
        KeyValueObjectInterfaceFactory $keyValueObjectFactory,
        OrderDataProvider $orderDataProvider,
        TotalExtensionInterfaceFactory $totalExtensionFactory
    ) {
        $this->shippingExtensionFactory = $shippingExtensionFactory;
        $this->serviceDataFactory = $serviceDataFactory;
        $this->packageDataFactory = $packageDataFactory;
        $this->keyValueObjectFactory = $keyValueObjectFactory;
        $this->orderDataProvider = $orderDataProvider;
        $this->totalExtensionFactory = $totalExtensionFactory;
    }

    /**
     * For DHL shipments, add the service information, custom product attributes, item data and
     * all customs data to the shipment.
     *
     * @param ShippingBuilder $shippingBuilder
     * @param ShippingInterface $shipping
     *
     * @return ShippingInterface
     * @see \Dhl\ShippingCore\Model\ShipmentRequest\RequestModifier::modifyPackage for package/data structure
     *
     */
    public function afterCreate(
        ShippingBuilder $shippingBuilder,
        ShippingInterface $shipping = null
    ): ShippingInterface {
        if (!$shipping) {
            return $shipping;
        }

        /** @var Address $orderAddress */
        $orderAddress = $shipping->getAddress();
        if (!$orderAddress) {
            return $shipping;
        }

        $extensionAttributes = $shipping->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->shippingExtensionFactory->create();
        }

        // create a temporary shipment for the order to be able to
        $order = $orderAddress->getOrder();

        $orderCarrierData = $this->orderDataProvider->getShippingOptions($order);
        if ($orderCarrierData === null) {
            return $shipping;
        }

        /** @var KeyValueObjectInterface[] $package */
        $package = [];
        /** @var ServiceDataInterface[] $services */
        $services = [];

        // Process general package options
        foreach ($orderCarrierData->getPackageOptions() as $shippingOption) {
            if ($shippingOption->getAvailable()) {
                foreach ($shippingOption->getInputs() as $input) {
                    // drop empty default values (meaning there was no preconfigured value)
                    if (empty($input->getDefaultValue())
                        || $input->getCode() === PackageContainerInputDataProcessor::CONTAINER_INPUT_CODE
                    ) {
                        continue;
                    }

                    $package[] = $this->keyValueObjectFactory->create(
                        [
                            KeyValueObjectInterface::KEY => $input->getCode(),
                            KeyValueObjectInterface::VALUE => $input->getDefaultValue(),
                        ]
                    );
                }
            }
        }

        // Process service options
        foreach ($orderCarrierData->getServiceOptions() as $serviceOption) {
            if ($serviceOption->getAvailable()) {
                $serviceDetails = [];
                foreach ($serviceOption->getInputs() as $input) {
                    // filter services that are not enabled (default off)
                    if ($input->getCode() === 'enabled' && (int) $input->getDefaultValue() !== 1) {
                        continue;
                    }
                    $serviceDetails[] = $this->keyValueObjectFactory->create(
                        [
                            KeyValueObjectInterface::KEY => $input->getCode(),
                            KeyValueObjectInterface::VALUE => $input->getDefaultValue(),
                        ]
                    );
                }
                if (!empty($serviceDetails)) {
                    $services[] = $this->serviceDataFactory->create(
                        [
                            ServiceDataInterface::CODE => $serviceOption->getCode(),
                            ServiceDataInterface::DETAILS => $serviceDetails,
                        ]
                    );
                }
            }
        }

        $packageData = $this->packageDataFactory->create(
            [
                ShippingOptionInterface::PACKAGE => $package,
                ShippingOptionInterface::SERVICES => $services,
            ]
        );

        $extensionAttributes->setDhlgwShippingOptions($packageData);
        $shipping->setExtensionAttributes($extensionAttributes);

        if (!$shipping->getTotal()) {
            return $shipping;
        }

        $totalsExtensionAttributes = $shipping->getTotal()->getExtensionAttributes();
        if (!$totalsExtensionAttributes) {
            $totalsExtensionAttributes = $this->totalExtensionFactory->create();
        }

        $totalsExtensionAttributes->setBaseDhlgwAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_FIELD_NAME)
        );
        $totalsExtensionAttributes->setBaseDhlgwAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_BASE_INCL_TAX_FIELD_NAME)
        );
        $totalsExtensionAttributes->setDhlgwAdditionalFee(
            $order->getData(TotalsManager::ADDITIONAL_FEE_FIELD_NAME)
        );
        $totalsExtensionAttributes->setDhlgwAdditionalFeeInclTax(
            $order->getData(TotalsManager::ADDITIONAL_FEE_INCL_TAX_FIELD_NAME)
        );

        $shipping->getTotal()->setExtensionAttributes($totalsExtensionAttributes);

        return $shipping;
    }
}
