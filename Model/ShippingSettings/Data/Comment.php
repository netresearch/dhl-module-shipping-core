<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\ShippingSettings\Data;

use Dhl\ShippingCore\Api\Data\ShippingSettings\ShippingOption\CommentInterface;

/**
 * Class Comment
 *
 * @author Max Melzer <max.melzer@netresearch.de>
 */
class Comment implements CommentInterface
{
    /**
     * @var string
     */
    private $content = '';

    /**
     * @var string|null
     */
    private $footnoteId;

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

    /**
     * @param string $content
     */
    public function setContent(string $content)
    {
        $this->content = $content;
    }

    /**
     * @param string|null $footnoteId
     */
    public function setFootnoteId(string $footnoteId)
    {
        $this->footnoteId = $footnoteId;
    }
}
