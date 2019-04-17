<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Selection\ServiceSelectionInterface;

/**
 * Class ServiceSelection
 *
 * @package Dhl\ShippingCore\Api\Data
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ServiceSelection implements ServiceSelectionInterface
{
    /**
     * @var string
     */
    private $serviceCode;

    /**
     * @var string
     */
    private $inputCode;

    /**
     * @var string
     */
    private $inputValue;

    /**
     * ServiceSelection constructor.
     *
     * @param string $serviceCode
     * @param string $inputCode
     * @param string $inputValue
     */
    public function __construct(string $serviceCode, string $inputCode, string $inputValue)
    {
        $this->serviceCode = $serviceCode;
        $this->inputCode = $inputCode;
        $this->inputValue = $inputValue;
    }

    /**
     * @return string
     */
    public function getServiceCode(): string
    {
        return $this->serviceCode;
    }

    /**
     * @return string
     */
    public function getInputCode(): string
    {
        return $this->inputCode;
    }

    /**
     * @return string
     */
    public function getInputValue(): string
    {
        return $this->inputValue;
    }
}
