<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Selection\ValidationRuleInterface;

/**
 * Class ValidationRule
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
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
