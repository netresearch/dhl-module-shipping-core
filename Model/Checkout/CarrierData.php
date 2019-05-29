<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;

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
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $packageOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $itemOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $serviceOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\MetadataInterface
     */
    private $metadata;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[]
     */
    private $compatibilityData;

    /**
     * CarrierData constructor.
     *
     * @param string $code
     * @param ShippingOptionInterface[] $packageOptions
     * @param ShippingOptionInterface[] $itemOptions
     * @param ShippingOptionInterface[] $serviceOptions
     * @param MetadataInterface $metadata
     * @param CompatibilityInterface[] $compatibilityData
     */
    public function __construct(
        string $code,
        MetadataInterface $metadata,
        array $packageOptions,
        array $itemOptions,
        array $serviceOptions,
        array $compatibilityData
    ) {
        $this->code = $code;
        $this->packageOptions = $packageOptions;
        $this->itemOptions = $itemOptions;
        $this->serviceOptions = $serviceOptions;
        $this->metadata = $metadata;
        $this->compatibilityData = $compatibilityData;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return ShippingOptionInterface[]
     */
    public function getPackageOptions(): array
    {
        return $this->packageOptions;
    }

    /**
     * @return ShippingOptionInterface[]
     */
    public function getItemOptions(): array
    {
        return $this->itemOptions;
    }

    /**
     * @return ShippingOptionInterface[]
     */
    public function getServiceOptions(): array
    {
        return $this->serviceOptions;
    }

    /**
     * @return MetadataInterface
     */
    public function getMetadata(): MetadataInterface
    {
        return $this->metadata;
    }

    /**
     * @return CompatibilityInterface[]
     */
    public function getCompatibilityData(): array
    {
        return $this->compatibilityData;
    }
}
