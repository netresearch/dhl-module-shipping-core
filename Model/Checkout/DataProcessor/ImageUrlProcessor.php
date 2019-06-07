<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Model\Checkout\AbstractProcessor;
use Magento\Framework\View\Asset\Repository;

/**
 * Class ImageUrlProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class ImageUrlProcessor extends AbstractProcessor
{
    /**
     * @var Repository
     */
    private $assetRepo;

    /**
     * ImageUrlProcessor constructor.
     *
     * @param Repository $assetRepo
     */
    public function __construct(Repository $assetRepo)
    {
        $this->assetRepo = $assetRepo;
    }

    /**
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
        if ($metadata->getImageUrl()) {
            $url = $this->assetRepo->getUrlWithParams(
                $metadata->getImageUrl(),
                ['area' => 'frontend']
            );
            $metadata->setImageUrl($url);
        }

        return $metadata;
    }
}
