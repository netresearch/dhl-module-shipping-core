<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\View\Asset\Repository;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\View\Element\Template;

/**
 * Class CustomInformation
 *
 * @package   Dhl\ShippingCore\Block\Adminhtml
 * @author    Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link      http://www.netresearch.de/
 */
class CustomInformation extends Field
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * CustomInformation constructor.
     *
     * @param Context    $context
     * @param Repository $repository
     */
    public function __construct(Context $context, Repository $repository)
    {
        $this->repository = $repository;

        parent::__construct($context);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element): string
    {
        $this->addChild(
            'custom_information_copy',
            Template::class,
            [
                'template' => $element->getData('field_config')['copy_template'],
            ]
        );

        return $this->toHtml();
    }

    /**
     * @return string
     */
    public function getTemplate(): string
    {
        return 'Dhl_ShippingCore::system/config/customInformation.phtml';
    }

    /**
     * @return string
     */
    public function getLogo(): string
    {
        return $this->repository->getUrl('Dhl_ShippingCore::images/logo.svg');
    }
}
