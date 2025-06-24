<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\ViewModel\Adminhtml\System;

use Dhl\ShippingCore\Model\Config\ItemValidator\Section;
use Dhl\ShippingCore\Model\Config\ModuleConfig;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class InfoBox implements ArgumentInterface
{
    /**
     * @var ModuleConfig
     */
    private $config;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    public function __construct(
        ModuleConfig $config,
        UrlInterface $urlBuilder,
        RequestInterface $request,
        StoreManagerInterface $storeManager
    ) {
        $this->config = $config;
        $this->urlBuilder = $urlBuilder;
        $this->request = $request;
        $this->storeManager = $storeManager;
    }

    private function getStoreId(): int
    {
        $storeId = $this->request->getParam('store');
        $websiteId = $this->request->getParam('website');
        if (!$storeId && !$websiteId) {
            return Store::DEFAULT_STORE_ID;
        }

        if ($storeId) {
            return (int) $storeId;
        }

        try {
            return (int) $this->storeManager->getWebsite($websiteId)->getDefaultStore()->getId();
        } catch (LocalizedException) {
            return Store::DEFAULT_STORE_ID;
        }
    }

    public function getModuleVersion(): string
    {
        return $this->config->getModuleVersion();
    }

    public function getParcelProcessingConfigUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'shipping',
                '_fragment' => 'shipping_parcel_processing-link',
            ]
        );
    }

    public function getBatchProcessingConfigUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'adminhtml/system_config/edit',
            [
                'section' => 'shipping',
                '_fragment' => 'shipping_batch_processing-link',
            ]
        );
    }

    public function getConfigValidationUrl(): string
    {
        return $this->urlBuilder->getUrl(
            'nrshipping/config/validate',
            [
                'section' => Section::CODE,
                'store' => $this->getStoreId(),
            ]
        );
    }
}
