<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Service\CompatibilityInterface;

/**
 * Class ServiceCompatibility
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class Compatibility implements CompatibilityInterface
{
    /**
     * @var bool
     */
    private $incompatibilityRule;

    /**
     * @var bool
     */
    private $hideSubjects;

    /**
     * @var string[]
     */
    private $subjects;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * Compatibility constructor.
     *
     * @param string[] $subjects
     * @param string $errorMessage
     * @param bool $incompatibilityRule
     * @param bool $hideSubjects
     */
    public function __construct(
        array $subjects,
        string $errorMessage,
        bool $incompatibilityRule = false,
        bool $hideSubjects = false
    ) {
        $this->subjects = $subjects;
        $this->errorMessage = $errorMessage;
        $this->incompatibilityRule = $incompatibilityRule;
        $this->hideSubjects = $hideSubjects;
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
}
