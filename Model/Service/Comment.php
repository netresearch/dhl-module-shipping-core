<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Service\CommentInterface;

/**
 * Class Comment
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class Comment implements CommentInterface
{
    /**
     * @var string HTML string to render inside the comment
     */
    private $content;

    /**
     * @var bool
     */
    private $hasFootnote;

    /**
     * Comment constructor.
     *
     * @param string $content
     * @param bool $hasFootnote
     */
    public function __construct(string $content, bool $hasFootnote)
    {
        $this->content = $content;
        $this->hasFootnote = $hasFootnote;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return bool
     */
    public function hasFootnote(): bool
    {
        return $this->hasFootnote;
    }
}
