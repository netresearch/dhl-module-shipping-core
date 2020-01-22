<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data\ValueMap;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValueMap\InputValueInterface;

/**
 * Class InputValue
 *
 * A Map of an input code to a value.
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class InputValue implements InputValueInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $value;

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code)
    {
        $this->code = $code;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }
}
