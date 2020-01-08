<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\ShippingSettings\Processor\Checkout;

use Dhl\ShippingCore\Api\Data\ShippingSettings\MetadataInterface;

/**
 * Class MetadataProcessorInterface
 *
 * @api
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface MetadataProcessorInterface
{
    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     * @param int|null $storeId
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata, int $storeId = null): MetadataInterface;
}
