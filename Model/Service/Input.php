<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Selection\CommentInterface;
use Dhl\ShippingCore\Api\Data\Selection\InputInterface;
use Dhl\ShippingCore\Api\Data\Selection\OptionInterface;
use Dhl\ShippingCore\Api\Data\Selection\ValidationRuleInterface;

/**
 * Class Input
 *
 * @package Dhl\ShippingCore\Model\Service
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
     * @var string
     */
    private $label;

    /**
     * @var bool
     */
    private $labelVisible;

    /**
     * @var OptionInterface[]
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
     * @var int;
     */
    private $sortOrder;

    /**
     * @var ValidationRuleInterface[]
     */
    private $validationRules;

    /**
     * @var CommentInterface|null
     */
    private $comment;

    /**
     * Input constructor.
     *
     * @param string $inputType
     * @param string $code
     * @param string $label
     * @param string $defaultValue
     * @param bool $labelVisible
     * @param OptionInterface[] $options
     * @param string $tooltip
     * @param string $placeholder
     * @param int $sortOrder
     * @param ValidationRuleInterface[] $validationRules
     * @param CommentInterface|null $comment
     */
    public function __construct(
        string $inputType,
        string $code,
        string $label,
        string $defaultValue = '',
        bool $labelVisible = true,
        array $options = [],
        string $tooltip = '',
        string $placeholder = '',
        int $sortOrder = 0,
        array $validationRules = [],
        $comment = null
    ) {
        $this->inputType = $inputType;
        $this->code = $code;
        $this->label = $label;
        $this->defaultValue = $defaultValue;
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
