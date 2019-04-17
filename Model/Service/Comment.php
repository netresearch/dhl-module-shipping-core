<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Service;

use Dhl\ShippingCore\Api\Data\Selection\CommentInterface;

/**
 * Class Comment
 *
 * @package Dhl\ShippingCore\Model\Service
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Comment implements CommentInterface
{
    /**
     * @var string HTML string to render inside the comment
     */
    private $content;

    /**
     * @var string|null
     */
    private $footnoteId;

    /**
     * Comment constructor.
     *
     * @param string $content
     * @param string|null $footnoteId
     */
    public function __construct(string $content, $footnoteId = null)
    {
        $this->content = $content;
        $this->footnoteId = $footnoteId;
    }

    /**
     * @return string
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * @return string|null
     */
    public function getFootnoteId()
    {
        return $this->footnoteId;
    }
}
