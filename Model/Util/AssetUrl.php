<?php

/**
 * See LICENSE.md for license details.
 */

declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Util;

use Dhl\ShippingCore\Api\Util\AssetUrlInterface;
use Magento\Framework\App\Area;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\DesignInterface;

class AssetUrl implements AssetUrlInterface
{
    /**
     * @var DesignInterface
     */
    private $design;

    /**
     * @var ThemeProviderInterface
     */
    private $themeProvider;

    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * AssetUrl constructor.
     *
     * @param DesignInterface $design
     * @param ThemeProviderInterface $themeProvider
     * @param Repository $assetRepo
     */
    public function __construct(
        DesignInterface $design,
        ThemeProviderInterface $themeProvider,
        Repository $assetRepo
    ) {
        $this->design = $design;
        $this->themeProvider = $themeProvider;
        $this->assetRepo = $assetRepo;
    }

    public function get(string $assetId): string
    {
        $params = [];

        if (!in_array($this->design->getArea(), [Area::AREA_FRONTEND, Area::AREA_ADMINHTML], true)) {
            // if resource is requested in webapi scope, then allocate from frontend theme.
            $params = ['area' => Area::AREA_FRONTEND];
            $themeId = $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND);

            if (is_numeric($themeId)) {
                $params['themeModel'] = $this->themeProvider->getThemeById($themeId);
            } else {
                $params['theme'] = $themeId;
            }
        }

        return $this->assetRepo->getUrlWithParams($assetId, $params);
    }
}
