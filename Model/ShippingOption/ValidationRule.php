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
     * @var mixed|mixed[]
     */
    private $params;

    /**
     * ValidationRule constructor.
     *
     * @param string $name
     * @param mixed $params
     */
    public function __construct(string $name, $params = [])
    {
        $this->name = $name;
        $this->params = $params;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return mixed|mixed[]
     */
    public function getParams()
    {
        return $this->params;
    }
}
