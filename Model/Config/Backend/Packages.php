<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Backend;

use Dhl\ShippingCore\Model\ShippingBox\Package;
use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

/**
 * Class Packages
 *
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Packages extends ArraySerialized
{
    /**
     * @return ArraySerialized
     */
    public function beforeSave(): ArraySerialized
    {
        $value = $this->getValue();
        if (is_array($value) && !array_key_exists(Package::KEY_IS_DEFAULT, $value) && count($value) > 1) {
            $value[Package::KEY_IS_DEFAULT] = '';
            $this->setValue($value);
        }

        return parent::beforeSave();
    }
}
