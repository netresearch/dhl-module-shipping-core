<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Config\Source;

use Magento\Config\Model\Config\Backend\Serialized\ArraySerialized;

/**
 * Class Packages
 *
 * @package Dhl\ShippingCore\Model\Config\Source
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
class Packages extends ArraySerialized
{
    /**
     * Use json encoding instead of serialisation
     *
     * @override
     * @return void
     */
    protected function _afterLoad(): void
    {
        if (!is_array($this->getValue())) {
            $value = $this->getValue();
            $this->setValue(empty($value) ? '' : json_decode($value, true));
        }
    }

}
