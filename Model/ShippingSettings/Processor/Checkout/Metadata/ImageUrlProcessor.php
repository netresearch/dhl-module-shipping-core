<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Processor\Checkout\Metadata;

use Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;
use Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout\MetadataProcessorInterface;
use Dhl\ShippingCore\Api\Util\AssetUrlInterface;

class ImageUrlProcessor implements MetadataProcessorInterface
{
    /**
     * @var AssetUrlInterface
     */
    private $assetUrl;

    /**
     * ImageUrlProcessor constructor.
     *
     * @param AssetUrlInterface $assetUrl
     */
    public function __construct(AssetUrlInterface $assetUrl)
    {
        $this->assetUrl = $assetUrl;
    }

    /**
     * Convert the image ID to its actual image URL in the current theme context.
     *
     * Note that the asset repository has a bug in Magento 2.2 which prevents calculating the correct image url
     * when called from a different area than frontend or adminhtml (e.g. webapi_rest).
     * Area emulation does not help either as the theme does not get properly initialized.
     * The workaround is to load the configured frontend theme manually.
     *
     * @param MetadataInterface $metadata
     * @param int|null $storeId
     *
     * @return MetadataInterface
     *
     * @see \Magento\Framework\View\Asset\Repository::updateDesignParams
     */
    public function process(MetadataInterface $metadata, int $storeId = null): MetadataInterface
    {
        $imageId = $metadata->getImageUrl();
        if (!$imageId) {
            return $metadata;
        }

        $metadata->setImageUrl($this->assetUrl->get($imageId));
        return $metadata;
    }
}
