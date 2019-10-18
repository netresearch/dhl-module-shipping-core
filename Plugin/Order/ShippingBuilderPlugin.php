<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Plugin\Order;

use Dhl\ShippingCore\Api\Data\KeyValueObjectInterface;
use Dhl\ShippingCore\Api\Data\KeyValueObjectInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Sales\ServiceDataInterface;
use Dhl\ShippingCore\Api\Data\Sales\ServiceDataInterfaceFactory;
use Dhl\ShippingCore\Api\Data\Sales\ShippingOptionInterface;
use Dhl\ShippingCore\Api\Data\Sales\ShippingOptionInterfaceFactory;
use Dhl\ShippingCore\Model\ShippingOption\OrderDataProvider;
use Magento\Sales\Api\Data\ShippingExtensionFactory;
use Magento\Sales\Api\Data\ShippingInterface;
use Magento\Sales\Model\Order\Address;
use Magento\Sales\Model\Order\ShippingBuilder;

/**
 * Class ShippingBuilderPlugin
 *
 * @package Dhl\ShippingCore\Plugin\Order
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
     * ShippingBuilderPlugin constructor.
     *
     * @param ShippingExtensionFactory $shippingExtensionFactory
     * @param ServiceDataInterfaceFactory $serviceDataFactory
     * @param ShippingOptionInterfaceFactory $packageDataFactory
     * @param KeyValueObjectInterfaceFactory $keyValueObjectFactory
     * @param OrderDataProvider $orderDataProvider
     */
    public function __construct(
        ShippingExtensionFactory $shippingExtensionFactory,
        ServiceDataInterfaceFactory $serviceDataFactory,
        ShippingOptionInterfaceFactory $packageDataFactory,
        KeyValueObjectInterfaceFactory $keyValueObjectFactory,
        OrderDataProvider $orderDataProvider
    ) {
        $this->shippingExtensionFactory = $shippingExtensionFactory;
        $this->serviceDataFactory = $serviceDataFactory;
        $this->packageDataFactory = $packageDataFactory;
        $this->keyValueObjectFactory = $keyValueObjectFactory;
        $this->orderDataProvider = $orderDataProvider;
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
                    if (empty($input->getDefaultValue())) {
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

        $extensionAttributes->setDhlgw($packageData);

        $shipping->setExtensionAttributes($extensionAttributes);

        return $shipping;
    }
}
