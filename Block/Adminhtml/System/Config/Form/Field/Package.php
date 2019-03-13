<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Dhl\ShippingCore\Model\Package as PackageModel;
use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Element\BlockInterface;

/**
 * Class Package
 *
 * @package Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field
 * @author  Andreas Müller <andreas.mueller@netresearch.de>
 * @license https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
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
     * @var PackageDefault
     */
    private $templateRenderer;

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
                'renderer' => $this->getTemplateRenderer(),
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
        $id = $this->getElement()->getScopeId() !== '' ? $this->getElement()->getScopeId() : 0;

        return (string)$this->config->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $id
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
     * @return PackageDefault|BlockInterface
     * @throws LocalizedException
     */
    private function getTemplateRenderer()
    {
        if (!$this->templateRenderer) {
            $this->templateRenderer = $this->getLayout()->createBlock(
                Template::class,
                'globalwebservices.mypackage.defaultPackage',
                [
                    'data' => [
                        'template' => 'Dhl_ShippingCore::system/config/defaultPackage.phtml',
                        'is_render_to_js_template' => true,
                    ],
                ]
            );
            $this->templateRenderer->setClass('package');
        }

        return $this->templateRenderer;
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
