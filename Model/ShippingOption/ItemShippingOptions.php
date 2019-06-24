<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingOption;

use Dhl\ShippingCore\Api\Data\ShippingOption\ItemShippingOptionsInterface;

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
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
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
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[]
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
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface[] $shippingOptions
     *
     * @return void
     */
    public function setShippingOptions(array $shippingOptions)
    {
        $this->shippingOptions = $shippingOptions;
    }
}
