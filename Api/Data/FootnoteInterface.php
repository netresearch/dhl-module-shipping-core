<?php
/**
 * See LICENSE.md for license details.
 */
declare(strict_types=1);

namespace Dhl\ShippingCore\Api\Data;

use Dhl\ShippingCore\Api\Data\ShippingOption\ShippingOptionInterface;

/**
 * Interface FootnoteInterface
 *
 * A DTO with rendering information for shipping option footnotes.
 *
 * @api
 * @package Dhl\ShippingCore\Api\Data
 */
interface FootnoteInterface
{
    /**
     * Retrieve the unique id of the shipping option footnote
     *
     * @return string
     */
    public function getId(): string;

    /**
     * Retrieve the HTML content of the footnote
     *
     * @return string
     */
    public function getContent(): string;

    /**
     * Retrieve a list of shipping option codes the footnote references
     *
     * @see ShippingOptionInterface
     * @return string[]
     */
    public function getSubjects(): array;

    /**
     * If this returns true, the footnote should only be displayed once all subjects are selected
     *
     * @return bool
     */
    public function isSubjectsMustBeSelected(): bool;

    /**
     * If this returns true, the footnote should only be displayed once all subjects are available for selection
     *
     * @return bool
     */
    public function isSubjectsMustBeAvailable(): bool;

    /**
     * @param string $id
     *
     * @return void
     */
    public function setId(string $id);

    /**
     * @param string $content
     *
     * @return void
     */
    public function setContent(string $content);

    /**
     * @param string[] $subjects
     *
     * @return void
     */
    public function setSubjects(array $subjects);

    /**
     * @param bool $subjectsMustBeSelected
     *
     * @return void
     */
    public function setSubjectsMustBeSelected(bool $subjectsMustBeSelected);

    /**
     * @param bool $subjectsMustBeAvailable
     *
     * @return void
     */
    public function setSubjectsMustBeAvailable(bool $subjectsMustBeAvailable);
}
