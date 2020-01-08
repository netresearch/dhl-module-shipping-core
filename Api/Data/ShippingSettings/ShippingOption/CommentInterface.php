<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption;

/**
 * Interface CommentInterface
 *
 * A DTO that represents a comment to be rendered in a shipping options area.
 *
 * @api
 */
interface CommentInterface
{
    /**
     * Retrieve the text content of the comment.
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Optionally retrieve the unique id of the footnote assigned to the content.
     *
     * @return string|null
     */
    public function getFootnoteId();

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content);

    /**
     * @param string $id
     *
     * @return void
     */
    public function setFootnoteId(string $id);
}
