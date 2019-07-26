<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model;

use Magento\Framework\DataObject;

/**
 * Class Package
 *
 * @package Dhl\ShippingCore\Model
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Package extends DataObject
{
    const KEY_HEIGHT = 'height';
    const KEY_WEIGHT = 'weight';
    const KEY_LENGTH = 'length';
    const KEY_WIDTH = 'width';
    const KEY_SORT_ORDER = 'sort_order';
    const KEY_TITLE = 'title';
    const KEY_IS_DEFAULT = 'is_default';
    const KEY_ID = 'id';

    /**
     * @return float
     */
    public function getWeight(): float
    {
        return (float) $this->getData(self::KEY_WEIGHT);
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return (string) $this->getData(self::KEY_TITLE);
    }

    /**
     * @return float
     */
    public function getLength(): float
    {
        return (float) $this->getData(self::KEY_LENGTH);
    }

    /**
     * @return float
     */
    public function getHeight(): float
    {
        return (float) $this->getData(self::KEY_HEIGHT);
    }

    /**
     * @return float
     */
    public function getWidth(): float
    {
        return (float) $this->getData(self::KEY_WIDTH);
    }

    /**
     * @return int
     */
    public function getSortOrder(): int
    {
        return (int) $this->getData(self::KEY_SORT_ORDER);
    }

    /**
     * @return bool
     */
    public function isDefault(): bool
    {
        return (bool) $this->getData(self::KEY_IS_DEFAULT);
    }
}
