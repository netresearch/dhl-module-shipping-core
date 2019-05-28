<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\InputInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface;
use Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface;

/**
 * Class Input
 *
 * @package Dhl\ShippingCore\Model
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Input implements InputInterface
{
    /**
     * @var string
     */
    private $inputType;

    /**
     * @var string
     */
    private $code;

    /**
     * @var string
     */
    private $defaultValue;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $labelVisible;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface[]
     */
    private $options;

    /**
     * @var string
     */
    private $tooltip;

    /**
     * @var string
     */
    private $placeholder;

    /**
     * @var int
     */
    private $sortOrder;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface[]
     */
    private $validationRules;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface|null
     */
    private $comment;

    /**
     * Input constructor.
     *
     * @param string $inputType
     * @param string $code
     * @param string $label
     * @param string $defaultValue
     * @param bool $disabled
     * @param bool $labelVisible
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\OptionInterface[] $options
     * @param string $tooltip
     * @param string $placeholder
     * @param int $sortOrder
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface[] $validationRules
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface|null $comment
     */
    public function __construct(
        string $inputType,
        string $code,
        string $label,
        string $defaultValue,
        bool $disabled,
        bool $labelVisible,
        array $options,
        string $tooltip,
        string $placeholder,
        int $sortOrder,
        array $validationRules,
        $comment = null
    ) {
        $this->inputType = $inputType;
        $this->code = $code;
        $this->label = $label;
        $this->defaultValue = $defaultValue;
        $this->disabled = $disabled;
        $this->labelVisible = $labelVisible;
        $this->options = $options;
        $this->tooltip = $tooltip;
        $this->placeholder = $placeholder;
        $this->sortOrder = $sortOrder;
        $this->validationRules = $validationRules;
        $this->comment = $comment;
    }

    /**
     * @return string
     */
    public function getInputType(): string
    {
        return $this->inputType;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @return string
     */
    public function getDefaultValue(): string
    {
        return $this->defaultValue;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return bool
     */
    public function hasLabelVisible(): bool
    {
        return $this->labelVisible;
    }

    /**
     * @return OptionInterface[]
     */
    public function getOptions(): array
    {
        return $this->options;
    }

    /**
     * @return string
     */
    public function getTooltip(): string
    {
        return $this->tooltip;
    }

    /**
     * @return string
     */
    public function getPlaceholder(): string
    {
        return $this->placeholder;
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return $this->sortOrder;
    }

    /**
     * @return ValidationRuleInterface[]
     */
    public function getValidationRules(): array
    {
        return $this->validationRules;
    }

    /**
     * @return CommentInterface|null
     */
    public function getComment()
    {
        return $this->comment;
    }
}
