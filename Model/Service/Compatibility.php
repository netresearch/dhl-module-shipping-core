<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Selection\CompatibilityInterface;

/**
 * Class ServiceCompatibility
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
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
    private $masters;

    /**
     * @var bool
     */
    private $incompatibilityRule;

    /**
     * @var bool
     */
    private $hideSubjects;

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
