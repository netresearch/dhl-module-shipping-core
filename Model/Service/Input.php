<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Service\CommentInterface;
use Dhl\ShippingCore\Api\Data\Service\InputInterface;
use Dhl\ShippingCore\Api\Data\Service\OptionInterface;
use Dhl\ShippingCore\Api\Data\Service\ValidationRuleInterface;

/**
 * Class Input
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
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
     * @var mixed
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
     * @var string[][]
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
     * @param mixed $defaultValue
     * @param string $label
     * @param bool $labelVisible
     * @param OptionInterface[] $options
     * @param string $tooltip
     * @param string $placeholder
     * @param int $sortOrder
     * @param \string[][] $validationRules
     * @param CommentInterface|null $comment
     */
    public function __construct(
        string $inputType,
        string $code,
        $defaultValue,
        string $label,
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
        $this->defaultValue = $defaultValue;
        $this->label = $label;
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
     * @return bool|float|int|string
     */
    public function getDefaultValue()
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
