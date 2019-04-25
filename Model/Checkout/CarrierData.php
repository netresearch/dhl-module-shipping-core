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
    private $carrierCode;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
     */
    private $shippingOptions;

    /**
     * @var \Dhl\ShippingCore\Api\Data\Checkout\MetadataInterface
     */
    private $metadata;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[]
     */
    private $compatibilityData = [];

    /**
     * CarrierData constructor.
     *
     * @param string $carrierCode
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[] $shippingOptions
     * @param \Dhl\ShippingCore\Api\Data\Checkout\MetadataInterface $metadata
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface[] $compatibilityData
     */
    public function __construct(
        string $carrierCode,
        array $shippingOptions,
        MetadataInterface $metadata,
        array $compatibilityData = []
    ) {
        $this->carrierCode = $carrierCode;
        $this->shippingOptions = $shippingOptions;
        $this->metadata = $metadata;
        $this->compatibilityData = $compatibilityData;
    }

    /**
     * @return string
     */
    public function getCarrierCode(): string
    {
        return $this->carrierCode;
    }

    /**
     * @return ShippingOptionInterface[]
     */
    public function getShippingOptions(): array
    {
        return $this->shippingOptions;
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
