<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout\CheckoutDataProcessor;

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

    public function processMetadata(array $metadata, string $countryId, string $postalCode, int $scopeId = null): array
    {
        if (isset($metadata['imageUrl'])) {
            $url = $this->assetRepo->getUrlWithParams(
                $metadata['imageUrl'],
                ['area' => 'frontend']
            );
            $metadata['imageUrl'] = $url ?: $metadata['imageUrl'];
        }

        return $metadata;
    }
}
