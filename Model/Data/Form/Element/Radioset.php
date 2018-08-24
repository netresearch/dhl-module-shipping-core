<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Data\Form\Element;

use Magento\Framework\Data\Form\Element\Radios;

/**
 * Class Radioset
 *
 * Implementation of a radio set input element that works inside the Magento system configuration.
 * Used by entering the class name into the "type" attribute of a system.xml field element.
 *
 * @package Dhl\ShippingCore\Model
 */
class Radioset extends Radios
{
    /**
     * Add a display none style since the css directive that hides the original input element is missing in
     * system_config.
     *
     * @param mixed $value
     * @return string
     */
    public function getStyle($value)
    {
        return 'display:none';
    }
}
