<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Block\Adminhtml\System\Config\Form\Field;

use Magento\Config\Block\System\Config\Form\Field;
use Magento\Backend\Block\Template\Context;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\Module\ModuleList;

/**
 * Class CustomInformation
 *
 * @package Dhl\ShippingCore\Block\Adminhtml
 * @author Ronny Gertler <ronny.gertler@netresearch.de>
 * @copyright 2018 Netresearch GmbH & Co. KG
 * @link http://www.netresearch.de/
 */
class CustomInformation extends Field
{
    /**
     * @var Repository
     */
    private $repository;

    /**
     * @var ModuleList
     */
    private $moduleList;

    /**
     * CustomInformation constructor.
     *
     * @param Context    $context
     * @param Repository $repository
     * @param ModuleList $moduleList
     */
    public function __construct(Context $context, Repository $repository, ModuleList $moduleList)
    {
        $this->repository = $repository;
        $this->moduleList = $moduleList;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element): string
    {
        $moduleVersion = $this->moduleList->getOne('Dhl_Express')['setup_version'];
        $logo          = $this->repository->getUrl('Dhl_Express::images/logo.svg');

        $html = $this->getLayout()
            ->createBlock(\Magento\Framework\View\Element\Template::class)
            ->setModuleVerion($moduleVersion)
            ->setLogo($logo)
            ->setTemplate('Dhl_ShippingCore::system/config/customInformation.phtml')
            ->toHtml();

        return $html;
    }
}
