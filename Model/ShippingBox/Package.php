<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingBox;

use Magento\Framework\DataObject;

class Package extends DataObject
{
    const KEY_HEIGHT = 'height';
    const KEY_WEIGHT = 'weight';
    const KEY_LENGTH = 'length';
    const KEY_WIDTH = 'width';
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
     * @return bool
     */
    public function isDefault(): bool
    {
        return (bool) $this->getData(self::KEY_IS_DEFAULT);
    }
}
