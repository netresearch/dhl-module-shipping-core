<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Service\OptionInterface;

/**
 * Class Option
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class Option implements OptionInterface
{
    /**
     * @var string
     */
    private $label;

    /**
     * @var string
     */
    private $value;

    /**
     * @var bool
     */
    private $disabled;

    /**
     * Option constructor.
     *
     * @param string $label
     * @param string $value
     * @param bool $disabled
     */
    public function __construct(string $label, string $value, bool $disabled)
    {
        $this->label = $label;
        $this->value = $value;
        $this->disabled = $disabled;
    }

    /**
     * @return string
     */
    public function getLabel(): string
    {
        return $this->label;
    }

    /**
     * @return string
     */
    public function getValue(): string
    {
        return $this->value;
    }

    /**
     * @return bool
     */
    public function isDisabled(): bool
    {
        return $this->disabled;
    }
}
