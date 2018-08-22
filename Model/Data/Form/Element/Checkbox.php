<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Data\Form\Element;

use Magento\Framework\Data\Form\Element\Checkbox as CoreCheckbox;

/**
 * Class Checkbox
 *
 * Implementation of a checkbox boolean input element that works inside the Magento system configuration.
 * Used by entering the class name into the "type" attribute of a system.xml field element.
 *
 * @package Dhl\ShippingCore\Model
 */
class Checkbox extends CoreCheckbox
{
    private const PSEUDO_POSTFIX = '_pseudo'; // used to create the hidden input id.

    /**
     * @return string
     */
    public function getElementHtml(): string
    {
        $this->setIsChecked((bool)$this->getData('value'));
        $this->setData('after_element_js', $this->getJsHtml());

        return parent::getElementHtml();
    }

    /**
     * Add a hidden input whose value is kept in sync with the checked status of the checkbox.
     *
     * @return string
     */
    protected function getJsHtml(): string
    {
        $html = '<input type="hidden" id="%s" value="%s"/>
        <script>
            (function() {
                let checkbox = document.getElementById("%s");
                let hidden = document.getElementById("%s");
                /** Make the hidden input the submitted one. **/
                hidden.name = checkbox.name;
                checkbox.name = "";
                /** keep the hidden input value in sync with the checkbox. **/
                checkbox.addEventListener("change", function (event) {
                    hidden.value = event.target.checked ? "1" : "0";
                });    
            })();   
        </script>';

        return sprintf(
            $html,
            $this->getHtmlId() . self::PSEUDO_POSTFIX,
            $this->getIsChecked() ? '1' : '0',
            $this->getHtmlId(),
            $this->getHtmlId() . self::PSEUDO_POSTFIX
        );
    }
}
