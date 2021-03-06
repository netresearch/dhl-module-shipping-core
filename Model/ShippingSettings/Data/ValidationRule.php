<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ValidationRuleInterface;

class ValidationRule implements ValidationRuleInterface
{
    /**
     * @var string
     */
    private $name = '';

    /**
     * @var mixed
     */
    private $param = null;

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
