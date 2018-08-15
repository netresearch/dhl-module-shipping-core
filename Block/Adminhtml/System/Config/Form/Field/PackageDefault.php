<?php
/**
 * Created by PhpStorm.
 * User: andreas
 * Date: 14.08.18
 * Time: 13:47
 */

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\Data\Form\Element\Radio;
use Magento\Framework\View\Element\AbstractBlock;

class PackageDefault extends AbstractBlock
{
    /**
     * @return string
     */
    protected function _toHtml(): string
    {
        $elId = $this->getInputId();
        $elName = $this->getInputName();
        $column = $this->getColumn();

        $name = ' name="'.str_replace('[<%- _id %>]', '', $elName).'"';
        $size = ($column['size'] ? 'size="' . $column['size'] . '"' : '');
        $style = ($column['style'] ? ' style="' . $column['style'] . '"' : '');
        $class = ' class="'.($column['class'] ?? 'input-radio').'"';

        return '<input type="radio" value="<%-_id %>" id="' . $elId . '"' . $name . $class . $style . $size . '/>';
    }
}
