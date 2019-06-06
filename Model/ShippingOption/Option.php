<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface;

/**
 * Class Option
 *
 * @package Dhl\ShippingCore\Model\ShippingOption
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Option implements OptionInterface
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * Option constructor.
     *
     * @param string $label
     * @param string $id
     * @param string $value
     * @param bool $disabled
     */
    public function __construct(string $id, string $label = '', string $value = '', bool $disabled = false)
    {
        $this->id = $id;
        $this->label = $label;
        $this->value = $value;
        $this->disabled = $disabled;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string $label
     */
    public function setLabel(string $label)
    {
        $this->label = $label;
    }

    /**
     * @param string $value
     */
    public function setValue(string $value)
    {
        $this->value = $value;
    }

    /**
     * @param bool $disabled
     */
    public function setDisabled(bool $disabled)
    {
        $this->disabled = $disabled;
    }
}
