<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Api\Data\Service;

/**
 * Interface CommentInterface
 *
 * @package Dhl\ShippingCore\Api
 */
interface CommentInterface
{
    /**
     * @return string
     */
    public function getContent(): string;

    /**
     * @return bool
     */
    public function hasFootnote(): bool;
}
