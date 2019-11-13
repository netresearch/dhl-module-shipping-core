<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption\Input;

use Dhl\ShippingCore\Api\Data\ShippingOption\ValueMapInterface;

/**
 * Class ValueMap
 *
 * Maps a source input value to a list of "input code" => "value" maps
 *
 * This can is used to let an input directly change the values of other inputs,
 * for example updating the package dimensions when selecting a package type.
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ValueMap implements ValueMapInterface
{
    /**
     * @var string
     */
    private $sourceValue;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ValueMap\InputValueInterface[]
     */
    private $inputValues = [];

    /**
     * @return string
     */
    public function getSourceValue(): string
    {
        return $this->sourceValue;
    }

    /**
     * @param string $sourceValue
     */
    public function setSourceValue(string $sourceValue)
    {
        $this->sourceValue = $sourceValue;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ValueMap\InputValueInterface[]
     */
    public function getInputValues(): array
    {
        return $this->inputValues;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ValueMap\InputValueInterface[] $inputValues
     */
    public function setInputValues(array $inputValues)
    {
        $this->inputValues = $inputValues;
    }
}
