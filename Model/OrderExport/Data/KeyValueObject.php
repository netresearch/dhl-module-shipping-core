<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\OrderExport\Data;

use Dhl\ShippingCore\Api\Data\OrderExport\KeyValueObjectInterface;

class KeyValueObject implements KeyValueObjectInterface
{

    /**
     * @var string
     */
    private $key;

    /**
     * @var string|float|boolean|integer
     */
    private $value;

    /**
     * KeyValueObject constructor.
     *
     * @param string $key
     * @param bool|float|int|string $value
     */
    public function __construct(string $key, $value)
    {
        $this->key = $key;
        $this->value = $value;
    }

    /**
     * @return string
     */
    public function getKey(): string
    {
        return $this->key;
    }

    /**
     * @param string $key
     * @return $this|KeyValueObjectInterface
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return bool|float|int|string
     */
    public function getValue()
    {
        return $this->value;
    }

    /**
     * @param string $value
     * @return $this|KeyValueObjectInterface
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }
}
