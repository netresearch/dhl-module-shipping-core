<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface ServiceMetadataInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
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
     * @return \Dhl\ShippingCore\Api\Data\Selection\CommentInterface[]
     */
    public function getCommentsBefore(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Selection\CommentInterface[]
     */
    public function getCommentsAfter(): array;

    /**
     * @return \Dhl\ShippingCore\Api\Data\Checkout\FootnoteInterface[]
     */
    public function getFootnotes(): array;
}
