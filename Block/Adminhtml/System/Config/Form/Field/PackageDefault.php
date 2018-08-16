<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Framework\View\Element\AbstractBlock;

/**
 * Class PackageDefault
 *
 * @package Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link    https://www.netresearch.de/
 */
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

        return '<input type="radio" value="<%-_id %>" id="' . $elId . '"' . $name . $class . $style . $size . ' />';
    }
}
