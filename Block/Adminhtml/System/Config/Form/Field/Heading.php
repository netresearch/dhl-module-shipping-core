<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;

/**
 * Class Heading
 *
 * @package Dhl\ShippingCore\Block\Adminhtml
 * @author Paul Siedler <paul.siedler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class Heading extends Field
{
    /**
     * Retrieve HTML markup for given form element
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element): string
    {
        $comment = $element->getData('comment');
        if ($comment) {
            $comment = "<p class='comment'>$comment</p>";
        }
        $html = sprintf('<td colspan="5"><h4>%s</h4>%s</td>', $element->getData('label'), $comment);

        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Decorate field row html
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @param string $html
     *
     * @return string
     */
    protected function _decorateRowHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element, $html)
    {
        return '<tr class="system-fieldset-sub-head" id="row_' . $element->getHtmlId() . '">' . $html . '</tr>';
    }
}
