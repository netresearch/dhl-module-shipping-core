<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Selection;

/**
 * Interface CommentInterface
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface CommentInterface
{
    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return string|null
     */
    public function getFootnoteId();
}
