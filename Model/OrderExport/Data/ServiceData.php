<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\OrderExport\Data;

use Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;
use Dhl\ShippingCore\Api\Data\OrderExport\ServiceDataInterface;

/**
 * Class ServiceData
 *
 * @package Dhl\ShippingCore\Model\WebApi\Data
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class ServiceData implements ServiceDataInterface
{
    /**
     * @var string
     */
    private $code;

    /**
     * @var KeyValueObjectInterface[]
     */
    private $details;

    /**
     * ServiceData constructor.
     *
     * @param string $code
     * @param KeyValueObjectInterface[] $details
     */
    public function __construct(string $code, array $details)
    {
        $this->code = $code;
        $this->details = $details;
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     * @return $this
     */
    public function setCode(string $code)
    {
        $this->code = $code;

        return $this;
    }

    /**
     * @return KeyValueObjectInterface[]
     */
    public function getDetails(): array
    {
        return $this->details;
    }

    /**
     * @param KeyValueObjectInterface[] $details
     * @return $this
     */
    public function setDetails(array $details)
    {
        $this->details = $details;

        return $this;
    }

}
