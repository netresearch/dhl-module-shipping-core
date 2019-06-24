<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\MetadataInterface;

/**
 * Class CarrierData
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class CarrierData implements CarrierDataInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var \Dhl\ShippingCore\Api\Data\MetadataInterface
     */
    private $metadata;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $packageOptions = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface[]
     */
    private $itemOptions = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $serviceOptions = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[]
     */
    private $compatibilityData = [];

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\MetadataInterface
     */
    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    public function getPackageOptions(): array
    {
        return $this->packageOptions;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface[]
     */
    public function getItemOptions(): array
    {
        return $this->itemOptions;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    public function getServiceOptions(): array
    {
        return $this->serviceOptions;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[]
     */
    public function getCompatibilityData(): array
    {
        return $this->compatibilityData;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\MetadataInterface $metadata
     */
    public function setMetadata(MetadataInterface $metadata)
    {
        $this->metadata = $metadata;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[] $packageOptions
     */
    public function setPackageOptions(array $packageOptions)
    {
        $this->packageOptions = $packageOptions;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface[] $itemOptions
     */
    public function setItemOptions(array $itemOptions)
    {
        $this->itemOptions = $itemOptions;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[] $serviceOptions
     */
    public function setServiceOptions(array $serviceOptions)
    {
        $this->serviceOptions = $serviceOptions;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[] $compatibilityData
     */
    public function setCompatibilityData(array $compatibilityData)
    {
        $this->compatibilityData = $compatibilityData;
    }
}
