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

/**
 * Class AssetUrl
 *
 * @author Andreas Müller <andreas.mueller@netresearch.de>
 * @link   https://www.netresearch.de/
 */
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
            $themeId = $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND);
            $params = [
                'area' => Area::AREA_FRONTEND,
                'themeModel' => $this->themeProvider->getThemeById($themeId),
            ];
        }

        return $this->assetRepo->getUrlWithParams($assetId, $params);
    }
}
