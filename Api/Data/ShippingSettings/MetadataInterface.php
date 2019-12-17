<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings;

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
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsBefore(): array;

    /**
     * Get a list of Comment objects to display at the bottom of the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[]
     */
    public function getCommentsAfter(): array;

    /**
     * Get a list of footnotes to display at the bottom of the shipping options area.
     *
     * @return \Dhl\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[]
     */
    public function getFootnotes(): array;

    /**
     * @param string $imageUrl
     *
     * @return void
     */
    public function setImageUrl(string $imageUrl);

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsBefore
     *
     * @return void
     */
    public function setCommentsBefore(array $commentsBefore);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface[] $commentsAfter
     *
     * @return void
     */
    public function setCommentsAfter(array $commentsAfter);

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingSettings\FootnoteInterface[] $footnotes
     *
     * @return void
     */
    public function setFootnotes(array $footnotes);
}
