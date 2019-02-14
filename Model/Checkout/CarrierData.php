<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\ServiceCompatibilityInterface;
use Dhl\ShippingCore\Api\Data\Checkout\ServiceMetadataInterface;
use Dhl\ShippingCore\Api\Data\Service\ServiceInterface;

/**
 * Class CarrierData
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class CarrierData implements CarrierDataInterface
{
    /**
     * @var string
     */
    private $carrierCode;

    /**
     * @var ServiceInterface[]
     */
    private $serviceData;

    /**
     * @var ServiceMetadataInterface
     */
    private $serviceMetadata;

    /**
     * @var ServiceCompatibilityInterface[]
     */
    private $serviceCompatibilityData;

    /**
     * CarrierData constructor.
     *
     * @param string $carrierCode
     * @param ServiceInterface[] $serviceData
     * @param ServiceMetadataInterface $serviceMetadata
     * @param ServiceCompatibilityInterface[] $serviceCompatibilityData
     */
    public function __construct(
        string $carrierCode,
        array $serviceData,
        ServiceMetadataInterface $serviceMetadata,
        array $serviceCompatibilityData
    ) {
        $this->carrierCode = $carrierCode;
        $this->serviceData = $serviceData;
        $this->serviceMetadata = $serviceMetadata;
        $this->serviceCompatibilityData = $serviceCompatibilityData;
    }

    /**
     * @return string
     */
    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\Service\ServiceInterface[]
     */
    public function getServiceData(): array
    {
        return $this->serviceData;
    }

    /**
     * @return ServiceMetadataInterface
     */
    public function getServiceMetadata(): ServiceMetadataInterface
    {
        return $this->serviceMetadata;
    }

    /**
     * @return ServiceCompatibilityInterface[]
     */
    public function getServiceCompatibilityData(): array
    {
        return $this->serviceCompatibilityData;
    }
}
