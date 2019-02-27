<?php
/**
 * See LICENSE.md for license details.
 */

namespace Dhl\ShippingCore\Model\Checkout;

use Dhl\ShippingCore\Api\Data\Checkout\FootnoteInterface;
use Dhl\ShippingCore\Api\Data\Checkout\ServiceMetadataInterface;
use Dhl\ShippingCore\Api\Data\Service\CommentInterface;
use Dhl\ShippingCore\Model\Service\Comment;
use Magento\Framework\Model\AbstractModel;

/**
 * Class ServiceMetadata
 *
 * @package Dhl\ShippingCore\Model\Checkout
 * @author    Max Melzer <max.melzer@netresearch.de>
 * @copyright 2019 Netresearch DTT GmbH
 * @link      http://www.netresearch.de/
 */
class ServiceMetadata implements ServiceMetadataInterface
{
    /**
     * @var string
     */
    private $imageUrl;

    /**
     * @var string
     */
    private $title;

    /**
     * @var CommentInterface[]
     */
    private $commentsBefore;

    /**
     * @var CommentInterface[]
     */
    private $commentsAfter;

    /**
     * @var FootnoteInterface[]
     */
    private $footnotes;

    /**
     * ServiceMetadata constructor.
     *
     * @param string $imageUrl
     * @param string $title
     * @param CommentInterface[] $commentsBefore
     * @param CommentInterface[] $commentsAfter
     * @param FootnoteInterface[] $footnotes
     */
    public function __construct(
        string $imageUrl,
        string $title,
        array $commentsBefore,
        array $commentsAfter,
        array $footnotes
    ) {
        $this->imageUrl = $imageUrl;
        $this->title = $title;
        $this->commentsBefore = $commentsBefore;
        $this->commentsAfter = $commentsAfter;
        $this->footnotes = $footnotes;
    }

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
     * @return CommentInterface[]
     */
    public function getCommentsBefore(): array
    {
        return $this->commentsBefore;
    }

    /**
     * @return CommentInterface[]
     */
    public function getCommentsAfter(): array
    {
        return $this->commentsAfter;
    }

    /**
     * @return FootnoteInterface[]
     */
    public function getFootnotes(): array
    {
        return $this->footnotes;
    }
}
