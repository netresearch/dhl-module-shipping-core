<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Dhl\ShippingCore\Model\Package as PackageModel;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;

/**
 * Class Package
 *
 * @method \Magento\Framework\Data\Form\Element\AbstractElement getElement
 *
 * @package Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Package extends AbstractFieldArray
{
    const PACKAGE_DEFAULT = 'packageDefault';

    /**
     * @var ScopeConfigInterface
     */
    private $config;

    /**
     * Package constructor.
     *
     * @param Context $context
     * @param array $data
     */
    public function __construct(
        Context $context,
        array $data = []
    ) {
        $this->config = $context->getScopeConfig();
        parent::__construct($context, $data);
    }

    /**
     * Prepare to render
     *
     * @throws LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->prepareElementValues();
        $this->addColumn(
            PackageModel::KEY_TITLE,
            [
                'label' => __('Title'),
                'style' => 'width:100px',
                'class' => 'required',
            ]
        );
        $this->addColumn(
            PackageModel::KEY_LENGTH,
            [
                'label' => __('Length %1', '<span>' . $this->getMeasureLengthUnit() . '</span>'),
                'style' => 'width:40px',
                'class' => 'validate-digits required',
            ]
        );
        $this->addColumn(
            PackageModel::KEY_WIDTH,
            [
                'label' => __('Width %1', '<span>' . $this->getMeasureLengthUnit() . '</span>'),
                'style' => 'width:40px',
                'class' => 'validate-digits required',
            ]
        );
        $this->addColumn(
            PackageModel::KEY_HEIGHT,
            [
                'label' => __('Height %1', '<span>' . $this->getMeasureLengthUnit() . '</span>'),
                'style' => 'width:40px',
                'class' => 'validate-number required',
            ]
        );

        $this->addColumn(
            PackageModel::KEY_WEIGHT,
            [
                'label' => __('Weight %1', '<span>' . $this->getWeightUnit() . '</span>'),
                'style' => 'width:40px',
                'class' => 'validate-number required',
            ]
        );

        $this->addColumn(
            PackageModel::KEY_SORT_ORDER,
            [
                'label' => __('Sort Order'),
                'style' => 'width:40px',
                'class' => 'validate-digits required',
            ]
        );

        $this->addColumn(
            PackageModel::KEY_IS_DEFAULT,
            [
                'label' => __('Set Default'),
            ]
        );

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Package');
    }

    /**
     * @return string
     */
    private function getWeightUnit(): string
    {
        $scopeId = $this->getElement()->getScopeId() !== '' ? $this->getElement()->getScopeId() : 0;

        return (string) $this->config->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $scopeId
        );
    }

    /**
     * @return string
     */
    private function getMeasureLengthUnit(): string
    {
        return $this->getWeightUnit() === 'kgs' ? 'cm' : 'inch';
    }

    /**
     * Render array cell for prototypeJS template.
     *
     * The `is_default` radio button needs special markup. The input ID is stripped of the element name to make it
     * a radio group spanning all package definitions.
     *
     * @param string $columnName
     * @return string
     * @throws \Exception
     */
    public function renderCellTemplate($columnName)
    {
        if ($columnName !== PackageModel::KEY_IS_DEFAULT) {
            return parent::renderCellTemplate($columnName);
        }

        $attributes = [
            'type' => 'radio',
            'value' => '<%- _id %>',
            'id' => $this->_getCellInputElementId('<%- _id %>', $columnName),
            'name' => str_replace('[<%- _id %>]', '', $this->_getCellInputElementName($columnName)),
            'size' => $this->_columns[$columnName]['size'],
            'style' => $this->_columns[$columnName]['style'],
            'class' => $this->_columns[$columnName]['class'] ?? 'input-radio',
        ];

        $attributes = array_reduce(
            array_keys($attributes),
            function ($output, $attributeKey) use ($attributes) {
                if (!empty($attributes[$attributeKey])) {
                    $output[] = sprintf('%s="%s"', $attributeKey, $attributes[$attributeKey]);
                }

                return $output;
            },
            []
        );

        $attributes = implode(' ', $attributes);

        return "<input $attributes />";
    }

    /**
     * Prepare the element data to consider the set default package
     */
    private function prepareElementValues()
    {
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $this->getElement();

        if ($element->getValue() && is_array($element->getValue())) {
            $elementValue = $element->getValue();
            $packageDefault = $elementValue[PackageModel::KEY_IS_DEFAULT] ?? '';
            unset($elementValue[PackageModel::KEY_IS_DEFAULT]);

            if ($packageDefault !== '') {
                $elementValue[$packageDefault][PackageModel::KEY_IS_DEFAULT] = $packageDefault ? true : false;
            }
            $element->setValue($elementValue);
        }
    }
}
