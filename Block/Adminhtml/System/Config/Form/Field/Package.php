<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Dhl\ShippingCore\Model\Config\CoreConfigInterface;
use Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field\PackageDefault;
use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field\FieldArray\AbstractFieldArray;

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
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    private $config;
    /**
     * @var PackageDefault
     */
    private $templateRenderer;

    /**
     * Rows cache
     *
     * @var array|null
     */
    private $arrayRowsCache;

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
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _prepareToRender()
    {
        $this->addColumn('title', [
            'label' => __('Title'),
            'style' => 'width:100px',
            'class' => 'required'
        ]);
        $this->addColumn('length', [
            'label' => __('Length <span>'.$this->getMeasureLengthUnit().'</span>'),
            'style' => 'width:40px',
            'class' => 'validate-digits required'
        ]);
        $this->addColumn('width', [
            'label' => __('Width <span>'.$this->getMeasureLengthUnit().'</span>'),
            'style' => 'width:40px',
            'class' => 'validate-digits required'
        ]);
        $this->addColumn('height', [
            'label' => __('Height <span>'.$this->getMeasureLengthUnit().'</span>'),
            'style' => 'width:40px',
            'class' => 'validate-number required'
        ]);

        $this->addColumn('weight', [
            'label' => __('Weight <span>'.$this->getWeightUnit().'</span>'),
            'style' => 'width:40px',
            'class' => 'validate-number required'
        ]);

        $this->addColumn('sortOrder', [
            'label' => __('Sort Order'),
            'style' => 'width:40px',
            'class' => 'validate-digits required'
        ]);

        $this->addColumn('packageDefault', [
            'label' => __('Set Default'),
            'renderer' => $this->getTemplateRenderer()
        ]);

        $this->_addAfter = false;
        $this->_addButtonLabel = __('Add Package');
    }

    /**
     * @return null|string
     */
    private function getWeightUnit(): ?string
    {
        $id = $this->getElement()->getScopeId() !== '' ? $this->getElement()->getScopeId() : 0;

        return $this->config->getValue(
            'general/locale/weight_unit',
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $id
        );
    }

    /**
     * @return null|string
     */
    private function getMeasureLengthUnit(): ?string
    {
        return $this->getWeightUnit() === 'kgs' ? 'cm' : 'inch';
    }

    /**
     * @return \Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field\PackageDefault|\Magento\Framework\View\Element\BlockInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getTemplateRenderer()
    {
        if (!$this->templateRenderer) {
            $this->templateRenderer = $this->getLayout()->createBlock(
                PackageDefault::class,
                '',
                ['data' => ['is_render_to_js_template' => true]]
            );
            $this->templateRenderer->setClass('package');
        }

        return $this->templateRenderer;
    }

    /**
     * @return array|null
     */
    public function getArrayRows(): ?array
    {
        if (null !== $this->arrayRowsCache) {
            return $this->arrayRowsCache;
        }
        $result = [];
        /** @var \Magento\Framework\Data\Form\Element\AbstractElement */
        $element = $this->getElement();

        if ($element->getValue() && is_array($element->getValue())) {
            $elementValue = $element->getValue();
            $packageDefault = $elementValue['packageDefault'];
            if ($packageDefault) {
                unset($elementValue['packageDefault']);
            }

            $elementValue[$packageDefault]['packageDefault'] = $packageDefault ? true : false ;

            foreach ($elementValue as $rowId => $row) {
                $rowColumnValues = [];
                foreach ($row as $key => $value) {
                    $row[$key] = $value;
                    $rowColumnValues[$this->_getCellInputElementId($rowId, $key)] = $row[$key];
                }
                $row['_id'] = $rowId;
                $row['column_values'] = $rowColumnValues;
                $result[$rowId] = new \Magento\Framework\DataObject($row);
                $this->_prepareArrayRow($result[$rowId]);
            }
        }
        $this->arrayRowsCache = $result;
        return $this->arrayRowsCache;
    }
}
