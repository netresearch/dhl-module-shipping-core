<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\ItemShippingOptionsInterface;

/**
 * Class ItemShippingOptions
 *
 * @package Dhl\ShippingCore\Model\ShippingOption
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ItemShippingOptions implements ItemShippingOptionsInterface
{
    /**
     * @var int
     */
    private $itemId = 0;

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    private $shippingOptions = [];

    /**
     * @return int
     */
    public function getItemId(): int
    {
        return $this->itemId;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[]
     */
    public function getShippingOptions(): array
    {
        return $this->shippingOptions;
    }

    /**
     * @param int $itemId
     *
     * @return void
     */
    public function setItemId(int $itemId)
    {
        $this->itemId = $itemId;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOptionInterface[] $shippingOptions
     *
     * @return void
     */
    public function setShippingOptions(array $shippingOptions)
    {
        $this->shippingOptions = $shippingOptions;
    }
}
