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
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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

    public function getWeight(): float
    {
        return (float)$this->getData(self::KEY_WEIGHT);
    }

    public function getTitle(): string
    {
        return (string)$this->getData(self::KEY_TITLE);
    }

    public function getLength(): float
    {
        return (float)$this->getData(self::KEY_LENGTH);
    }

    public function getHeight(): float
    {
        return (float)$this->getData(self::KEY_HEIGHT);
    }

    public function getSortOrder(): int
    {
        return (int)$this->getData(self::KEY_SORT_ORDER);
    }

    public function isDefault(): bool
    {
        return (bool)$this->getData(self::KEY_IS_DEFAULT);
    }

}
