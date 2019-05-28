<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\CarrierDataInterface;
use Dhl\ShippingCore\Api\Data\Checkout\MetadataInterface;
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
    private $packageLevelOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $itemLevelOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\Checkout\MetadataInterface
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
     * @param ShippingOptionInterface[] $packageLevelOptions
     * @param ShippingOptionInterface[] $itemLevelOptions
     * @param MetadataInterface $metadata
     * @param CompatibilityInterface[] $compatibilityData
     */
    public function __construct(
        string $code,
        MetadataInterface $metadata,
        array $packageLevelOptions,
        array $itemLevelOptions,
        array $compatibilityData
    ) {
        $this->code = $code;
        $this->packageLevelOptions = $packageLevelOptions;
        $this->itemLevelOptions = $itemLevelOptions;
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
    public function getPackageLevelOptions(): array
    {
        return $this->packageLevelOptions;
    }

    /**
     * @return ShippingOptionInterface[]
     */
    public function getItemLevelOptions(): array
    {
        return $this->itemLevelOptions;
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
