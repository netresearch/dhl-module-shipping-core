<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CompatibilityInterface;

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
     * @var string
     */
    private $triggerValue = '';

    /**
     * @var string
     */
    private $action;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId(string $id)
    {
        $this->id = $id;
    }

    /**
     * @return string[]
     */
    public function getSubjects(): array
    {
        return $this->subjects;
    }

    /**
     * @param string[] $subjects
     */
    public function setSubjects(array $subjects)
    {
        $this->subjects = $subjects;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }

    /**
     * @param string $errorMessage
     */
    public function setErrorMessage(string $errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return string[]
     */
    public function getMasters(): array
    {
        return $this->masters;
    }

    /**
     * @param string[] $masters
     */
    public function setMasters(array $masters)
    {
        $this->masters = $masters;
    }

    /**
     * @return string
     */
    public function getTriggerValue(): string
    {
        return $this->triggerValue;
    }

    /**
     * @param string $triggerValue
     */
    public function setTriggerValue(string $triggerValue)
    {
        $this->triggerValue = $triggerValue;
    }

    /**
     * @return string
     */
    public function getAction(): string
    {
        return $this->action;
    }

    /**
     * @param string $action
     */
    public function setAction(string $action)
    {
        $this->action = $action;
    }
}
