<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\ValidationRuleInterface;

/**
 * Class ValidationRule
 *
 * @package Dhl\ShippingCore\Model
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ValidationRule implements ValidationRuleInterface
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var mixed
     */
    private $param;

    /**
     * ValidationRule constructor.
     *
     * @param string $name
     * @param mixed $param
     */
    public function __construct(string $name = '', $param = null)
    {
        $this->name = $name;
        $this->param = $param;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|null
     */
    public function getParam()
    {
        return $this->param;
    }

    /**
     * @param string $name
     */
    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @param mixed $param
     */
    public function setParam($param)
    {
        $this->param = $param;
    }
}
