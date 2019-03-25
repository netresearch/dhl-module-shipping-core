<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShipmentRequest;

use Dhl\ShippingCore\Api\Data\ShipmentRequest\PackageInterface;

/**
 * Class Package
 *
 * @package Dhl\ShippingCore\Model
 */
class Package implements PackageInterface
{
    /**
     * Product used for the package, e.g. V01PAK, PLT, etc.
     *
     * @var string
     */
    private $productCode;

    /**
     * Packaging type, e.g. "C5 Letter", "Small Box", etc.
     *
     * @var string
     */
    private $containerType;

    /**
     * @var string
     */
    private $weightUom;

    /**
     * @var string
     */
    private $dimensionsUom;

    /**
     * @var float
     */
    private $weight;

    /**
     * @var float|null
     */
    private $length;

    /**
     * @var float|null
     */
    private $width;

    /**
     * @var float|null
     */
    private $height;

    /**
     * @var float|null
     */
    private $customsValue;

    /**
     * @var string
     */
    private $contentType;

    /**
     * @var string
     */
    private $contentDescription;

    /**
     * Package constructor.
     *
     * @param string $productCode
     * @param string $containerType
     * @param string $weightUom
     * @param string $dimensionsUom
     * @param float $weight
     * @param float|null $length
     * @param float|null $width
     * @param float|null $height
     * @param float|null $customsValue
     * @param string $contentType
     * @param string $contentDescription
     */
    public function __construct(
        string $productCode,
        string $containerType,
        string $weightUom,
        string $dimensionsUom,
        float $weight,
        float $length = null,
        float $width = null,
        float $height = null,
        float $customsValue = null,
        string $contentType = '',
        string $contentDescription = ''
    ) {
        $this->productCode = $productCode;
        $this->containerType = $containerType;
        $this->weightUom = $weightUom;
        $this->dimensionsUom = $dimensionsUom;
        $this->weight = $weight;
        $this->length = $length;
        $this->width = $width;
        $this->height = $height;
        $this->customsValue = $customsValue;
        $this->contentType = $contentType;
        $this->contentDescription = $contentDescription;
    }

    /**
     * Obtain product to be used for the package.
     *
     * @return string
     */
    public function getProductCode(): string
    {
        return $this->productCode;
    }

    /**
     * Obtain pre-defined packaging name.
     *
     * @return string
     */
    public function getContainerType(): string
    {
        return $this->containerType;
    }

    /**
     * Obtain weight unit of measurement.
     *
     * @return string
     */
    public function getWeightUom(): string
    {
        return $this->weightUom;
    }

    /**
     * Obtain dimensions unit of measurement.
     *
     * @return string
     */
    public function getDimensionsUom(): string
    {
        return $this->dimensionsUom;
    }

    /**
     * Obtain package weight.
     *
     * @return float
     */
    public function getWeight(): float
    {
        return $this->weight;
    }

    /**
     * Obtain package length (optional).
     *
     * @return float|null
     */
    public function getLength()
    {
        return $this->length;
    }

    /**
     * Obtain package width (optional).
     *
     * @return float|null
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * Obtain package height (optional).
     *
     * @return float|null
     */
    public function getHeight()
    {
        return $this->height;
    }

    /**
     * Obtain package customs value (optional).
     *
     * @return float|null
     */
    public function getCustomsValue()
    {
        return $this->customsValue;
    }

    /**
     * Obtain package customs declaration content type (optional).
     *
     * @return string
     */
    public function getContentType(): string
    {
        return $this->contentType;
    }

    /**
     * Obtain package customs declaration content description (optional).
     *
     * @return string
     */
    public function getContentDescription(): string
    {
        return $this->contentDescription;
    }
}
