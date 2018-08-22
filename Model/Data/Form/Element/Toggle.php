<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Data\Form\Element;

/**
 * Class Toggle
 *
 * Implementation of a checkbox boolean input element styled like a toggle that works inside the Magento system
 * configuration. Used by entering the class name into the "type" attribute of a system.xml field element.
 *
 * @package Dhl\ShippingCore\Model
 */
class Toggle extends Checkbox
{
    /**
     * Hide the default checkbox and add toggle class.
     *
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->setData('style', 'position:absolute; clip:rect(0,0,0,0); overflow:hidden');
        $this->addClass('admin__actions-switch-checkbox');

        return parent::getElementHtml();
    }

    /**
     * Add the switch label after the input.
     *
     * @return string
     */
    protected function getJsHtml(): string
    {
        return $this->getSwitchLabel() . parent::getJsHtml();
    }

    /**
     * @return string
     */
    private function getSwitchLabel(): string
    {
        $html = '<label for="%s" class="admin__actions-switch-label">
            <span class="admin__actions-switch-text" data-text-on="%s" data-text-off="%s"></span>
        </label>';

        return sprintf(
            $html,
            $this->getHtmlId(),
            __('Yes'),
            __('No')
        );
    }
}
