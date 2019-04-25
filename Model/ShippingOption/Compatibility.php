<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\CompatibilityInterface;

/**
 * Class Compatibility
 *
 * @package Dhl\ShippingCore\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Compatibility implements CompatibilityInterface
{
    /**
     * @var string[]
     */
    private $subjects;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var string[]
     */
    private $masters = [];

    /**
     * @var bool
     */
    private $incompatibilityRule = false;

    /**
     * @var bool
     */
    private $hideSubjects = false;

    /**
     * Compatibility constructor.
     *
     * @param string[] $subjects
     * @param string $errorMessage
     * @param string[] $masters
     * @param bool $incompatibilityRule
     * @param bool $hideSubjects
     */
    public function __construct(
        array $subjects,
        string $errorMessage,
        array $masters = [],
        bool $incompatibilityRule = false,
        bool $hideSubjects = false
    ) {
        $this->subjects = $subjects;
        $this->errorMessage = $errorMessage;
        $this->masters = $masters;
        $this->incompatibilityRule = $incompatibilityRule;
        $this->hideSubjects = $hideSubjects;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @return string[]
     */
    public function getMasters(): array
    {
        return $this->masters;
    }

    /**
     * @return bool
     */
    public function isIncompatibilityRule(): bool
    {
        return $this->incompatibilityRule;
    }

    /**
     * @return bool
     */
    public function isHideSubjects(): bool
    {
        return $this->hideSubjects;
    }
}
