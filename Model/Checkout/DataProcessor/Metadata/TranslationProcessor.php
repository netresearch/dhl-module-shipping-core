<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Model\Checkout\DataProcessor\Metadata;

use Dhl\ShippingCore\Api\Data\MetadataInterface;
use Dhl\ShippingCore\Model\Checkout\DataProcessor\MetadataProcessorInterface;

/**
 * Class TranslationProcessor
 *
 * @package Dhl\ShippingCore\Model\Checkout\DataProcessor
 * @author Max Melzer <max.melzer@netresearch.de>
 * @author Rico Sonntag <rico.sonntag@netresearch.de>
 */
class TranslationProcessor implements MetadataProcessorInterface
{
    /**
     * @param MetadataInterface $metadata
     *
     * @return MetadataInterface
     */
    public function process(MetadataInterface $metadata): MetadataInterface
    {
        // Comments before
        foreach ($metadata->getCommentsBefore() as $comment) {
            $comment->setContent(
                __($comment->getContent())->render()
            );
        }

        // Comments after
        foreach ($metadata->getCommentsAfter() as $comment) {
            $comment->setContent(
                __($comment->getContent())->render()
            );
        }

        // Title
        $metadata->setTitle(
            __($metadata->getTitle())->render()
        );

        // Footnotes
        foreach ($metadata->getFootnotes() as $footnote) {
            $footnote->setContent(
                __($footnote->getContent())->render()
            );
        }

        return $metadata;
    }
}
