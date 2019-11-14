<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Backend;

use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\LocalizedException;

/**
 * This class throws an exception if the user selects all entries from the drop off day list.
 * At least one entry must not be selected.
 *
 * @package Dhl\ShippingCore\Model
 * @author  Rico Sonntag <rico.sonntag@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class DropOffDays extends Value
{
    /**
     * @return $this|Value
     * @throws LocalizedException
     */
    public function beforeSave()
    {
        $noDropOffConfig = explode(',', (string)$this->getValue());

        if (count($noDropOffConfig) === 6) {
            throw new LocalizedException(__('You need to have at least one drop off day.'));
        }

        return parent::beforeSave();
    }
}
