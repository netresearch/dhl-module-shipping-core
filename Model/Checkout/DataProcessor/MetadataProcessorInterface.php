<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor;

use Dhl\ShippingCore\Api\Data\MetadataInterface;

/**
 * Class MetadataProcessorInterface
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
interface MetadataProcessorInterface
{
    /**
     * Receive shipping option metadata and modify it according to business logic.
     *
     * @param MetadataInterface $metadata
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata): MetadataInterface;
}
