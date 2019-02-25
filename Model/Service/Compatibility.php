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
     * @param bool $incompatibilityRule
     * @param string[] $subjects
     * @param string $errorMessage
     */
    public function __construct(bool $incompatibilityRule, array $subjects, string $errorMessage)
    {
        $this->incompatibilityRule = $incompatibilityRule;
        $this->subjects = $subjects;
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return bool
     */
    public function isIncompatibilityRule(): bool
    {
        return $this->incompatibilityRule;
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
