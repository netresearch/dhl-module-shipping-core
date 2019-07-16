<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\MetadataInterface;

/**
 * Class Metadata
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author  Max Melzer <max.melzer@netresearch.de>
 * @link    https://www.netresearch.de/
 */
class Metadata implements MetadataInterface
{
    /**
     * @var string
     */
    private $title = '';

    /**
     * @var string
     */
    private $imageUrl = '';

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    private $commentsBefore = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    private $commentsAfter = [];

    /**
     * @var \Dhl\ShippingCore\Api\Data\FootnoteInterface[]
     */
    private $footnotes = [];

    /**
     * @return string
     */
    public function getImageUrl(): string
    {
        return $this->imageUrl;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    public function getCommentsBefore(): array
    {
        return $this->commentsBefore;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[]
     */
    public function getCommentsAfter(): array
    {
        return $this->commentsAfter;
    }

    /**
     * @return \Dhl\ShippingCore\Api\Data\FootnoteInterface[]
     */
    public function getFootnotes(): array
    {
        return $this->footnotes;
    }

    /**
     * @param string $imageUrl
     */
    public function setImageUrl(string $imageUrl)
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @param string $title
     *
     * @return void
     */
    public function setTitle(string $title)
    {
        $this->title = $title;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[] $commentsBefore
     *
     * @return void
     */
    public function setCommentsBefore(array $commentsBefore)
    {
        $this->commentsBefore = $commentsBefore;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\ShippingOption\CommentInterface[] $commentsAfter
     *
     * @return void
     */
    public function setCommentsAfter(array $commentsAfter)
    {
        $this->commentsAfter = $commentsAfter;
    }

    /**
     * @param \Dhl\ShippingCore\Api\Data\FootnoteInterface[] $footnotes
     *
     * @return void
     */
    public function setFootnotes(array $footnotes)
    {
        $this->footnotes = $footnotes;
    }
}
