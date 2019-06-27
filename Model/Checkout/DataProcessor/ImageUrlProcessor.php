<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;
use Magento\Framework\App\Area;
use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Design\Theme\ThemeProviderInterface;
use Magento\Framework\View\DesignInterface;

/**
 * Class ImageUrlProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ImageUrlProcessor extends AbstractProcessor
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
     * ImageUrlProcessor constructor.
     *
     * @param DesignInterface $design
     * @param ThemeProviderInterface $themeProvider
     * @param Repository $assetRepo
     */
    public function __construct(DesignInterface $design, ThemeProviderInterface $themeProvider, Repository $assetRepo)
    {
        $this->design = $design;
        $this->themeProvider = $themeProvider;
        $this->assetRepo = $assetRepo;
    }

    /**
     * Convert the image ID to its actual image URL in the current theme context.
     *
     * Note that the asset repository has a bug in Magento 2.2 which prevents calculating the correct image url
     * when called from a different area than frontend or adminhtml (e.g. webapi_rest).
     * Area emulation does not help either as the theme does not get properly initialized.
     * The workaround is to load the configured frontend theme manually.
     *
     * @see \Magento\Framework\View\Asset\Repository::updateDesignParams
     *
     * @param MetadataInterface $metadata
     * @param string $countryId
     * @param string $postalCode
     * @param int|null $scopeId
     *
     * @return MetadataInterface
     */
    public function processMetadata(
        MetadataInterface $metadata,
        string $countryId,
        string $postalCode,
        int $scopeId = null
    ): MetadataInterface {
        $imageId = $metadata->getImageUrl();
        if (!$imageId) {
            return $metadata;
        }

        if (in_array($this->design->getArea(), [Area::AREA_FRONTEND, Area::AREA_ADMINHTML])) {
            $params = [];
        } else {
            $themeId = $this->design->getConfigurationDesignTheme(Area::AREA_FRONTEND);
            $params = [
                'area' => Area::AREA_FRONTEND,
                'themeModel' => $this->themeProvider->getThemeById($themeId),
            ];
        }

        $imageUrl = $this->assetRepo->getUrlWithParams($imageId, $params);
        $metadata->setImageUrl($imageUrl);

        return $metadata;
    }
}
