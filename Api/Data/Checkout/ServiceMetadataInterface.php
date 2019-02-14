<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface ServiceMetadataInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface ServiceMetadataInterface
{
    /**
     * @return string
     */
    public function getImageUrl(): string;

    /**
     * @return string
     */
    public function getTitle(): string;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Service\CommentInterface[]
     */
    public function getCommentsBefore(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Service\CommentInterface[]
     */
    public function getCommentsAfter(): array;
}
