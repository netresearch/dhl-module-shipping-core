<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\Checkout;

/**
 * Interface MetadataInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface MetadataInterface
{
    /**
     * Get the url for a logo or image to display in the shipping options area.
     *
     * @return string
     */
    public function getImageUrl(): string;

    /**
     * Get the title to display in the to display in the shipping options area.
     *
     * @return string
     */
    public function getTitle(): string;

    /**
     * Get a list of Comment objects to display at the top of the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    public function getCommentsBefore(): array;

    /**
     * Get a list of Comment objects to display at the bottom of the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    public function getCommentsAfter(): array;

    /**
     * Get a list of footnotes to display at the bottom of the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\Checkout\FootnoteInterface[]
     */
    public function getFootnotes(): array;
}
