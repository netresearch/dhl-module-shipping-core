<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface ValueMapInterface
 *
 * Maps a source input value to a list of "input code" => "value" maps
 *
 * This can is used to let an input directly change the values of other inputs,
 * for example updating the package dimensions when selecting a package type.
 *
 * @api
 */
interface ValueMapInterface
{
    /**
     * Get the value the source input should have to trigger this rule.
     *
     * @return string
     */
    public function getSourceValue(): string;

    /**
     * Get the mappings of input codes to values.
     * This is applied when the input source value matches self::getSourceValue()
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface[]
     */
    public function getInputValues(): array;

    /**
     * @param string $sourceValue
     *
     * @return void
     */
    public function setSourceValue(string $sourceValue);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface[] $inputValues
     *
     * @return void
     */
    public function setInputValues(array $inputValues);
}
