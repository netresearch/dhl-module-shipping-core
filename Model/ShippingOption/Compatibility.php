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
     * @var string
     */
    private $id = '';

    /**
     * @var string[]
     */
    private $subjects = [];

    /**
     * @var string
     */
    private $errorMessage = '';

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
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
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

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @param string[] $subjects
     */
    public function setSubjects(array $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @param string[] $masters
     */
    public function setMasters(array $masters)
    {
        $this->masters = $masters;
    }

    /**
     * @param bool $incompatibilityRule
     */
    public function setIncompatibilityRule(bool $incompatibilityRule)
    {
        $this->incompatibilityRule = $incompatibilityRule;
    }

    /**
     * @param bool $hideSubjects
     */
    public function setHideSubjects(bool $hideSubjects)
    {
        $this->hideSubjects = $hideSubjects;
    }
}
