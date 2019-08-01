<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest\RequestModifier;

use Dhl\ShippingCore\Api\Data\CarrierDataInterface;
use Dhl\ShippingCore\Api\PackagingOptionReaderInterface;
use Dhl\ShippingCore\Model\Packaging\PackagingDataProvider;
use Magento\Framework\Exception\LocalizedException;
use Magento\Sales\Model\Order\Shipment;

/**
 * Class PackagingOptionReader
 *
 * @package Dhl\ShippingCore\Model
 * @author  Christoph Aßmann <christoph.assmann@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class PackagingOptionReader implements PackagingOptionReaderInterface
{
    /**
     * @var PackagingDataProvider
     */
    private $packagingDataProvider;

    /**
     * @var Shipment
     */
    private $shipment;

    /**
     * @var CarrierDataInterface
     */
    private $carrierData;

    /**
     * PackagingOptionReader constructor.
     * @param PackagingDataProvider $packagingDataProvider
     * @param Shipment $shipment
     */
    public function __construct(PackagingDataProvider $packagingDataProvider, Shipment $shipment)
    {
        $this->packagingDataProvider = $packagingDataProvider;
        $this->shipment = $shipment;
    }

    /**
     * Initialize packaging data provider and extract carrier data.
     *
     * @return CarrierDataInterface
     * @throws LocalizedException
     */
    private function getCarrierData()
    {
        if (!$this->carrierData) {
            if (!isset($this->shipment)) {
                throw new LocalizedException(__('Cannot initialize packaging data, please provide shipment.'));
            }

            $packagingData = $this->packagingDataProvider->getData($this->shipment);
            $carriers = $packagingData->getCarriers();
            if (empty($carriers)) {
                throw new LocalizedException(__('Unable to load shipment request properties.'));
            }

            $this->carrierData = current($carriers);
        }

        return $this->carrierData;
    }

    /**
     * Read a package option from the packaging options identified by option code and input code.
     *
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getPackageOptionValue(string $optionCode, string $inputCode)
    {
        $packageOptions = $this->getCarrierData()->getPackageOptions();
        if (!isset($packageOptions[$optionCode])) {
            throw new LocalizedException(__('The package option "%1" is not available.', $optionCode));
        }

        $inputs = $packageOptions[$optionCode]->getInputs();
        if (!isset($inputs[$inputCode])) {
            throw new LocalizedException(__('The value "%1" is not available for package option "%2".', $inputCode, $optionCode));
        }

        return $inputs[$inputCode]->getDefaultValue();
    }

    /**
     * Read an item value from the packaging options.
     *
     * @param int $orderItemId
     * @param string $optionCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getItemOptionValue(int $orderItemId, string $optionCode, string $inputCode)
    {
        $itemOptions = $this->getCarrierData()->getItemOptions();
        if (!isset($itemOptions[$orderItemId])) {
            throw new LocalizedException(__('Options for order item "%1" are not available.', $orderItemId));
        }

        $shippingOptions = $itemOptions[$orderItemId]->getShippingOptions();
        if (!isset($shippingOptions[$optionCode])) {
            throw new LocalizedException(__('The item option "%1" is not available.', $optionCode));
        }

        $inputs = $shippingOptions[$optionCode]->getInputs();
        if (!isset($inputs[$inputCode])) {
            throw new LocalizedException(__('The value "%1" is not available for item option "%2".', $inputCode, $optionCode));
        }

        return $inputs[$inputCode]->getDefaultValue();
    }

    /**
     * Read a service value from the packaging options identified by service code and input code.
     *
     * @param string $serviceCode
     * @param string $inputCode
     * @return mixed
     * @throws LocalizedException
     */
    public function getServiceOptionValue(string $serviceCode, string $inputCode)
    {
        $serviceOptions = $this->getCarrierData()->getServiceOptions();
        if (!isset($serviceOptions[$serviceCode])) {
            throw new LocalizedException(__('The service option "%1" is not available.', $serviceCode));
        }

        $inputs = $serviceOptions[$serviceCode]->getInputs();
        if (!isset($inputs[$inputCode])) {
            throw new LocalizedException(__('The value "%1" is not available for service option "%2".', $inputCode, $serviceCode));
        }

        return $inputs[$inputCode]->getDefaultValue();
    }

    /**
     * Read all service option values.
     *
     * @return string[][]
     * @throws LocalizedException
     */
    public function getServiceOptionValues(): array
    {
        $services = [];
        foreach ($this->getCarrierData()->getServiceOptions() as $serviceCode => $serviceOption) {
            foreach ($serviceOption->getInputs() as $inputCode => $input) {
                $services[$serviceCode][$inputCode] = $input->getDefaultValue();
            }
        }

        return $services;
    }

    /**
     * Read package customs values
     *
     * @return array
     * @throws LocalizedException
     */
    public function getPackageCustomsValues():array
    {
        $customsValues = [];
        $packageOptions = $this->getCarrierData()->getPackageOptions();

        if (isset($packageOptions['packageCustoms'])) {
            foreach ($packageOptions['packageCustoms']->getInputs() as $optionCode => $input) {
                $customsValues[$optionCode] = $input->getDefaultValue();
            }
        }

        return $customsValues;
    }

    /**
     * Read item customs values
     *
     * @param int $orderItemId
     *
     * @return array
     * @throws LocalizedException
     */
    public function getItemCustomsValues(int $orderItemId): array
    {
        $customsValues = [];
        $itemOptions = $this->getCarrierData()->getItemOptions();
        $shippingOptions = $itemOptions[$orderItemId]->getShippingOptions();
        if (isset($shippingOptions['itemCustoms'])) {
            foreach ($shippingOptions['itemCustoms']->getInputs() as $inputCode => $input) {
                $customsValues[$inputCode] = $input->getDefaultValue();
            }
        }

        return $customsValues;
    }
}
